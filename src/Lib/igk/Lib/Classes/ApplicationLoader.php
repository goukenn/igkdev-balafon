<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ApplicationLoader.php
// @date: 20220803 13:48:54
// @desc: 

namespace IGK;

use Closure;
use IGK\Helper\IO;
use IGK\System\IO\Path as IGKPath;
use IGK\helper\StringUtility;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Traits\ClassFileVersionLoaderTrait;
use IGKEnvironment;
use IGKEvents;
use IGKException;
use ReflectionException;

require_once IGK_LIB_CLASSES_DIR . '/System/Traits/ClassFileVersionLoaderTrait.php';
require_once IGK_LIB_CLASSES_DIR . '/Server.php';
require_once IGK_LIB_CLASSES_DIR . '/System/IO/Path.php';

///<summary>core application loader </summary>
/**
 * 
 * @package 
 */
class ApplicationLoader
{
    use ClassFileVersionLoaderTrait;

    /**
     * instance loader initialized after boot
     * @var static
     */
    private static $sm_instance;
    /**
     * included application files
     * @var array
     */
    private $_included = [];
    /**
     * included changed
     * @var bool
     */
    private $_changed;

    /**
     * core loaded : to cache primary loading polyfill access
     * @var mixed
     */
    private $_coreload;

    /**
     * path manager handler
     * @var mixed
     */
    private $path;


    /**
     * load callable
     */
    private $callables = [];

    /**
     * 
     * @var string loading context
     */
    private $_context;
    /**
     * get instance application to after boot
     * @return static instance
     */
    public static function getInstance()
    {
        return self::$sm_instance;
    }
    private function getCacheFile()
    {
        return  IGK_LIB_DIR . "/.Caches/.included." . implode(".", array_filter([$this->_context, igk_environment()->getPhpCoreVersion()])) . ".cache";
    }

    private function __construct($context = null)
    {
        $this->_context = $context;
        register_shutdown_function(function () {
            // igk_wln("shut down --- ",$this->_changed, $this->_included);
            // error_log("[IGK] - application loader shut down");
            if (!defined("IGK_BASE_DIR") || defined('IGK_NO_LIB_CACHE')) {
                return;
            } 
            if ($this->_changed) {
                $m = implode("\n", array_map(function ($m) {
                    if (strpos($m, IGK_LIB_CLASSES_DIR) == 0) {
                        $m = str_replace(IGK_LIB_CLASSES_DIR, "IGK_LIB_CLASSES_DIR . '", $m) . "'";
                    }
                    return "require_once " . $m . ";";
                }, $this->_included));
                igk_io_w2file($this->getCacheFile(), "<?php\n" . $m . "");
           
                if (igk_getv($this->_load_classes, "c")) {
                    unset($this->_load_classes["c"]);
                    $path = self::GetLocalAppClassesCacheFile();              
                    igk_io_w2file($path, serialize($this->_load_classes));                
                }
            }
            igk_hook(IGKEvents::HOOK_SHUTDOWN, [$this]);
        });        
    } 
   
    /**
     * register class 
     * @param string $file 
     * @param string $classname 
     * @param null|string $version 
     * @return void 
     * @throws IGKException 
     */
    // public function registerClass(string $file, string $classname, ?string $version = null)
    // {
    //     $file = $file;
    //     if (empty($this->_load_classes)) {
    //         $this->_load_classes = ["cl" => [], "files" => [], "versions" => []];
    //     }
    //     $index = -1;
    //     if (!isset($this->_load_classes["cl"][$classname])) {
    //         $index = count($this->_load_classes["cl"]);
    //         $this->_load_classes["cl"][$classname] = $index;
    //         if (!($finfo = igk_getv($this->_load_classes["files"], $file))) {
    //             $finfo = (object)['p' => $file];
    //             $this->_load_classes["files"][$file] = $finfo;
    //         }
    //         $this->_load_classes["files"][$index] = $finfo;
    //     } else {
    //         $index = $this->_load_classes["cl"][$classname];
    //     }

    //     if (!isset($this->_load_classes["versions"][$index])) {
    //         if (!empty($version)) {
    //             $this->_load_classes["versions"][$index] = $version;
    //         }
    //     } else {
    //         $tv = $this->_load_classes["versions"][$index];
    //         if (!is_array($tv)) {
    //             $this->_load_classes["versions"][$index] = [$tv => 0];
    //         }
    //         if (empty($version)) {
    //             $version = "_"; // current version
    //         }
    //         $this->_load_classes["versions"][$index][$version] = $file;
    //     }
    //     $this->_load_classes["c"] = 1;
    // }
    // private function _initClassRegister()
    // {
    //     if (is_file($fc = $this->getClassesCacheFiles())) {
    //         $this->_load_classes = unserialize(file_get_contents($fc));
    //     }
    // }
    // private function getregisterClass($classname)
    // {
    //     if (empty($this->_load_classes)) {
    //         return;
    //     }
    //     if (!is_null($index = igk_getv($this->_load_classes["cl"], $classname))) {
    //         $finfo = $this->_load_classes["files"][$index];
    //         if ($tv = igk_getv($this->_load_classes["versions"], $index)) {
    //             // check for version to match
    //             list($major, $minor) = explode('.', PHP_VERSION);
    //             foreach ([$major . "." . $minor, $major, "_"] as $t) {
    //                 if (isset($tv[$t])) {
    //                     return $tv[$t];
    //                 }
    //             }
    //             return null;
    //         }
    //         return $finfo->p;
    //     }
    // }
    /**
     * register load
     * @param mixed $callable 
     * @param mixed $classdir 
     * @param int $priority 
     * @return void 
     */
    public static function RegisterAutoload($callable, $classdir = null, $priority = 20)
    {
        self::getInstance()->Load($callable, $priority, $classdir);
    }
    /**
     * register autoload callback
     * @param mixed $callable 
     * @return void 
     */
    private function Load($callable, $priority = 20, $classdir = null, $namespace = null)
    {
        $this->callables[] = get_defined_vars();
        $this->sorted = 1;
    }
    public function registerLoading($entryNS, $classdir, $priority = 20, &$refile = null): bool
    {
        $cl = &igk_environment()->createArray(IGKEnvironment::AUTO_LOAD_CLASS);
        if (!isset($cl[$classdir])) {
            $auto_register = false;
            $cl[$classdir] = compact("entryNS", "refile", "auto_register");
            $this->Load(function ($n) use ($classdir, &$cl) {
                $e_ns = igk_getv($cl[$classdir], "entryNS");
                $g = self::_TryLoadClasses([$n], $classdir, $e_ns, false, false);
                return $g;
            }, $priority, $classdir, $entryNS);
            return true;
        }
        return false;
    }

    private function _sort_priority($a, $b)
    {
        $g = strcmp((string)$b["namespace"], (string)$a["namespace"]);
        if ($g != 0) {
            return $g;
        }
        $x = $a['priority'];
        $y = $b['priority'];
        return $x == $y ? 0 : $y - $x / abs($y - $x);
    }
    private function _createAutoLoadClosure()
    {
        return function ($n) {
            // igk_wln('app loading ...'.$n. ' - '. igk_sys_request_time());
            if ($this->callables) {
                if ($this->sorted) {
                    usort($this->callables, [$this, '_sort_priority']);
                    $this->sorted = false;
                }
                foreach ($this->callables as $c) {
                    if (!is_dir($c["classdir"]) || (!empty($ns = $c["namespace"]) && (strpos($n, ltrim($ns . "\\", "\\")) !== 0))) {
                        continue;
                    }
                    $fc = $c["callable"];
                    if ($fc($n)) {
                        return 1;
                    }
                }
            }
            return self::LoadClass($n);
        };
    }
    /**
     * try load class name
     * @param string $classname 
     * @return int 
     */
    public static function TryLoad(string $classname)
    {
        $fc = self::$sm_instance->_createAutoLoadClosure();
        return $fc($classname);
    }
    /**
     * load system classes
     * @param array $classnames 
     * @return bool 
     * @throws IGKException 
     */
    public static function LoadClasses($classnames = [])
    {
        if (is_string($classnames)) {
            $classnames = [$classnames];
        }
        return self::_TryLoadClasses($classnames, IGK_LIB_CLASSES_DIR, \IGK::class);
    }

    /**
     * 
     * @param array $classnames 
     * @param mixed $path 
     * @param mixed $entryNS 
     * @param bool $throw raise exeception if not found
     * @param bool $auto_register auto register cache class if found.
     * @return bool 
     * @throws IGKException 
     */
    private static function _TryLoadClasses(array $classnames, $path, $entryNS = null,  $throw = false, $auto_register = true)
    {
        $included = null;
        $v_coreload  = !self::$sm_instance->_coreload;
        if ($v_coreload) {
            $included = &self::$sm_instance->_included;
        } else {
            $included = [];
        }

        list($major, $minor) = explode(".", PHP_VERSION);
        $resolv_class_versions =  [$major . "." . $minor, $major, ""];
        $cdir = null;
        $is_core  = $v_coreload || (IGK_LIB_CLASSES_DIR == $path);
        // $v_coreload && igk_wln("is core : ", $is_core, $path);
        $result = true;
        if ($entryNS) {
            if (is_string($entryNS))
                $entryNS = StringUtility::Uri($entryNS);
            else
                $entryNS = \IGK::class;
        }
        if (!is_array($path)) {
            $path = [$path];
        }
        $force_load = true;
        $core_ns = "IGK/";
        $php_ext = ".php";

        while ($cdir = array_shift($path)) {
            if (!is_dir($cdir)) {
                continue;
            }
            while ($result &&  ($classname = array_shift($classnames)) !== null) {
                //if (! $v_coreload ){
                if ($tpath = self::$sm_instance->getRegisterClass($classname)) {
                    $found = true;
                    require_once($tpath);
                    $result = $result && $found;
                    break;
                }
                //} 
                // load class method
                if ($force_load || (!class_exists($classname, false) && !trait_exists($classname, false) && !interface_exists($classname, false))) {
                    // igk_ilog("tryload:".$classname);
                    $n = $classname;
                    $f = StringUtility::Uri($n);
                    if ($is_core && (strpos($f, $core_ns) === 0)) {
                        $f = substr($f, 4);
                    }
                    if (!$is_core && $entryNS &&  (strpos($f, $entryNS) === 0)) {
                        $f = substr($f, strlen($entryNS) + 1);
                    }
                    $found = false;
                    foreach ($resolv_class_versions as $version) {
                        $files = [];
                        $ext = $php_ext;
                        if (empty($version)) {
                            $files[] = $cdir . "/" . $f . $ext;
                        } else {
                            $ext = ".{$version}" . $ext;
                            $files[] = $cdir . "/" . $f . $ext;
                            $files[] = $cdir . "/{$version}/" . $f . $php_ext;
                        }
                        while ($cf = array_shift($files)) {
                            if (isset($included[$cf]) || !is_file($cf)) {
                                continue;
                            }

                            require_once($cf);
                            if (
                                !class_exists($n, false) && !interface_exists($n, false)
                                && !trait_exists($n, false)
                            ) {
                                if ($throw) {
                                    igk_trace();
                                    igk_die("file {$cf} loaded but not content class|interface|trait {$n} definition", 1, 500);
                                }
                                $result = false;
                            }

                            if ($auto_register) {
                                // first version file founded
                                $included[$cf] = $cf;
                                if ($v_coreload) {
                                    self::$sm_instance->_changed = true;
                                }
                                self::$sm_instance->registerClass($cf, $classname, $version);
                            }
                            $found = true;
                            break;
                        }
                        if ($found)
                            break;
                    }
                    $result = $result && $found;
                }
            }
        }
        return $result;
    }
    public static function LoadClass($classname)
    {
        return self::LoadClasses([$classname]);
    }


    /**
     * boot loading application
     * @param string $type default is 'web'
     * @param array|object $bootoptions view IGKApplicationBootOptions for allowed properties 
     * @return IGKApplication 
     */
    public static function Boot(string $type = "web", $bootoptions = null)
    {
        // + protect
        static $initialize;
        $srv = Server::getInstance();
        $boot = false;
        if ($initialize === null) {
            self::$sm_instance =  new self($type);
            self::$sm_instance->_coreload  = false;
            $init_info = [
                "spl_auto_loader" => self::$sm_instance->_createAutoLoadClosure(), //::  [self::$sm_instance, '_auto_load']
            ];
            spl_autoload_register($init_info["spl_auto_loader"], true, true);
            $initialize = $init_info;
            $boot = true;
        }
        // + | Initialize environment  
        if ($boot) {
            $file = self::$sm_instance->getCacheFile();
            if (is_file($file)) {
                include($file);
            } else {
                // + | -----------------------------------------------------
                // + | Load traits according to version
                // + |  
                self::LoadClasses([
                    \IGK\System\Polyfill\ArrayAccessSelfTrait::class,
                    \IGK\System\Polyfill\CSSDefaultArrayAccess::class,
                    \IGK\System\Polyfill\IGKMediaArrayAccessTrait::class,
                    \IGK\System\Polyfill\IteratorTrait::class,
                    \IGK\System\Polyfill\ScriptAssocArrayAccessTrait::class,
                    \IGK\System\Polyfill\JsonSerializableTrait::class,
                    \IGK\System\Polyfill\EventPropertyArrayAccessTrait::class,
                    \IGK\System\Configuration\ConfigArrayAccessTrait::class,
                    \IGK\Controllers\ControllerUriTrait::class,
                    \IGK\System\IO\FileSystem::class,
                    \IGKIterator::class,
                    \IGKUserInfo::class,
                    \IIGKArrayObject::class,
                    \IGKResourceUriResolver::class,
                ]);
                self::$sm_instance->_changed = true;
            }
            self::$sm_instance->_coreload = true;
        }
  
        //return null;
        ($app = ApplicationFactory::Create($type)) || igk_die("failed to create application: " . $type);
        if ($boot) {
            $app->bootstrap($bootoptions, function()use($app){ 
                self::$sm_instance->bootApp($app);
            });
            self::$sm_instance->bootApp($app);
        } 
        // + | init local application register 
        self::$sm_instance->_initClassRegister();
        // + | -----------------------------------------------------
        // + | return the application 
        // + |  
        return $app;
    }
    /**
     * boot application loader 
     * @param mixed $app 
     * @return never 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function bootApp($app){
 
        if (self::$sm_instance->_resolvConstant()){            
            igk_hook(IGKEvents::HOOK_APP_BOOT, [$app]); 
        }
    }
    /**
     * resolv_constant
     * @return bool 
     * @throws IGKException 
     */
    private function _resolvConstant()
    {
        if (!empty($this->path)){
            return false;
        }
        self::InitConstants();       
        $this->path = IGKPath::getInstance();
        $package_dir = $this->path->getPackagesDir();
        // + | -----------------------------------------------------
        // + | Autoloading composer packages
        // + | 
        if (is_file($package_dir . "/composer.json") && is_file($package_dir . "/vendor/autoload.php")) {
            igk_environment()->getComposerLoader()->register($package_dir . "/vendor/autoload.php");
            // preload spl loading class
            // spl_autoload_unregister($initialize["spl_auto_loader"]);
            // require_once($package_dir . "/vendor/autoload.php");
            // spl_autoload_register($initialize["spl_auto_loader"], true, true);
        }
        return true;
    }
    /**
     * init application constants 
     * @return void 
     * @throws IGKException 
     */
    public static function InitConstants(){        
        $srv = igk_server();
        // igk_wln_e("bootstrap.... ", $boot );
        // + |-----------------------------------------------------------------------
        // + | mandatory constants protected base constant
        // + |         
        $bdir = defined("IGK_BASE_DIR") ? IGK_BASE_DIR : getcwd();

        if (!defined('IGK_APP_DIR')) {
            $dir = !empty($dir = $srv->IGK_APP_DIR) && is_dir($dir) ? $dir : $bdir;
            define("IGK_APP_DIR", $dir);
        }
        if (!defined('IGK_BASE_DIR')) {
            define("IGK_BASE_DIR", $bdir);
        }
        if (!defined("IGK_PROJECT_DIR")) {
            $dir = !empty($dir = $srv->IGK_PROJECT_DIR) && is_dir($dir) ? $dir : StringUtility::Dir(IGK_APP_DIR . "/" . IGK_PROJECTS_FOLDER);
            define("IGK_PROJECT_DIR", $dir);
        }
        if (!defined("IGK_MODULE_DIR")) {
            if (!empty($dir = $srv->IGK_MODULE_DIR) && is_dir($dir))
                define("IGK_MODULE_DIR", $dir);
        }
        if (!defined("IGK_PACKAGE_DIR")) {
            define("IGK_PACKAGE_DIR", IGK_APP_DIR . "/" . IGK_PACKAGES_FOLDER);
        }

        if (!defined("IGK_MODULE_DIR")) {
            define("IGK_MODULE_DIR", IGK_PACKAGE_DIR . "/" . IGK_MODULE_FOLDER);
        }
        if (defined('IGK_SESS_DIR') && (is_dir(IGK_SESS_DIR) || IO::CreateDir(IGK_SESS_DIR))) {
            ini_set("session.save_path", IGK_SESS_DIR);
        }

    }
    /**
     * core test classes loader callback
     * @return Closure 
     */
    public static function TestClassesLoaderCallback(){
        return  function($n){    
            $fix_path = function($p, $sep=DIRECTORY_SEPARATOR){
                if ($sep=="/"){
                    return str_replace("\\", "/", $p);
                }  
                return str_replace("/", "\\", $p);
            };
            $dir = IGK_LIB_DIR."/Lib/Tests/";
            if (strpos($n, $ns= \IGK\Tests::class)===0){
                $cl = substr($n, strlen($ns)+1);
                $f = $fix_path($dir.$cl.".php");       
                if (file_exists($f)){
                    include($f);
                    if (!class_exists($n, false)){
                        throw new \Exception("File exists but class not present");
                    }
                    return 1;
                }
            } 
            return 0;
        };
    }
}
