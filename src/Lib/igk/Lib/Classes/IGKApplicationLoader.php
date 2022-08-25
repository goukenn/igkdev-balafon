<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKApplicationLoader.php
// @date: 20220803 13:48:54
// @desc: 


use IGK\Helper\IO;
use IGK\System\IO\Path as IGKPath;
use IGK\helper\StringUtility;



///<summary>core application loader </summary>
class IGKApplicationLoader
{

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
     * @return IGKApplicationLoader instance
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
            // error_log("[IGK] - application loader shut down");
            if (!defined("IGK_BASE_DIR")) {
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
            }
            igk_hook(IGKEvents::HOOK_SHUTDOWN, [$this]);
        });
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
    public function registerLoading($entryNS, $classdir, $priority = 20, &$refile = null)
    {

        $cl = &igk_environment()->createArray(IGKEnvironment::AUTO_LOAD_CLASS);
        if (!isset($cl[$classdir])) {
            $cl[$classdir] = compact("entryNS", "refile");
            $this->Load(function ($n) use ($classdir, &$cl) {
                $e_ns = igk_getv($cl[$classdir], "entryNS");
                // if (!empty($e_ns) && (strpos($n, $e_ns . "\\") === 0)) {
                //      $n = substr($n, strlen($e_ns) + 1);
                // }
                $g = self::_TryLoadClasses([$n], $classdir, $e_ns, false);
                return $g;
            }, $priority, $classdir, $entryNS);
            // igk_environment()->set($key, $cl);
        }
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
    private function _auto_load($n)
    {

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
    }
    /**
     * try load class name
     * @param string $classname 
     * @return int 
     */
    public static function TryLoad(string $classname)
    {
        return self::getInstance()->_auto_load($classname);
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
     * @param bool $throw 
     * @return bool 
     * @throws IGKException 
     */
    private static function _TryLoadClasses(array $classnames, $path, $entryNS = null,  $throw = false)
    {
        // igk_wln_e("try load classes.");
        $included = null;
        $v_coreload  = !self::$sm_instance->_coreload;
        if ($v_coreload) {
            $included = &self::$sm_instance->_included;
        } else {
            $included = [];
        }

        list($major, $minor) = explode(".", PHP_VERSION);
        $resolv_class =  [$major . "." . $minor, $major, ""];
        $cdir = null;
        $is_core  = IGK_LIB_CLASSES_DIR == $path;
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
                    foreach ($resolv_class as $version) {
                        $files = [];
                        $ext = $php_ext;
                        if (empty($version)) {
                            $files[] = $cdir . "/" . $f . $ext;
                        } else {
                            $ext = ".{$version}" . $ext;
                            $files[] = $cdir . "/" . $f . $ext;
                            $files[] = $cdir . "/{$version}/" . $f . $php_ext;
                        }
                        while ($cf = array_shift($files)){
                                // }
                                // if (
                                //     file_exists($cf = ($cdir . "/" . $f . $ext)) ||
                                //     (!empty($version) && file_exists($cf = ($cdir . "/{$version}/" . $f . ".php")))
                                // ) {
                                if (!is_file($cf)){
                                    continue;
                                } 
                                require_once($cf);
                                $included[$cf] = $cf;
                                if ($v_coreload) {
                                    self::$sm_instance->_changed = true;
                                }
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
                                // first version file founded
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
     * @return IGKApplication 
     */
    public static function Boot($type = "web")
    {
        // + protect w
        static $initialize; 
        $srv = IGKServer::getInstance();
        $boot = false;
        if ($initialize === null) {
            self::$sm_instance =  new self($type);
            self::$sm_instance->_coreload  = false;
            $init_info= [
                "spl_auto_loader"=>[self::$sm_instance, '_auto_load']
            ];
            spl_autoload_register($init_info["spl_auto_loader"] , true, true);
            $initialize = $init_info;
            $boot = true;
    
        require_once __DIR__ . "/Helper/StringUtility.php";
        require_once __DIR__ . "/Helper/IO.php";
        require_once __DIR__ . "/System/IO/Path.php";
        require_once __DIR__ . "/IGKConstants.php";
        require_once __DIR__ . "/IGKObject.php";
        require_once __DIR__ . "/System/IO/FileWriter.php";
        require_once __DIR__ . "/IGKServer.php";
        require_once __DIR__ . "/Cache/CommonCache.php";
        require_once __DIR__ . "/Controllers/RootControllerBase.php";
        require_once __DIR__ . "/IGKApplicationFactory.php";
        require_once __DIR__ . "/IGKApplicationBase.php";
        require_once __DIR__ . "/IGKWebApplication.php";
        require_once __DIR__ . "/IGKLibraryBase.php";
        require_once __DIR__ . "/IGKRoutes.php";
        require_once __DIR__ . "/IGKSysUtil.php";
        require_once __DIR__ . "/Database/DbSchemas.php";
        require_once __DIR__ . "/Database/DbColumnInfo.php";
        require_once __DIR__ . "/IGKLog.php";
        require_once __DIR__ . "/IGKException.php";
        require_once __DIR__ . "/IGKObjStorage.php"; /* require library  */
    } 
        // + | Initialize environment  
        if ($boot) {
            $file = self::$sm_instance->getCacheFile();

            if (file_exists($file)) {
                include($file);
            } else {
                // + | -----------------------------------------------------
                // + | Load traits according to version
                // + | 
                self::LoadClasses([
                    IGK\System\Polyfill\ArrayAccessSelfTrait::class,
                    IGK\System\Polyfill\CSSDefaultArrayAccess::class,
                    IGK\System\Polyfill\IGKMediaArrayAccessTrait::class,
                    IGK\System\Polyfill\IteratorTrait::class,
                    IGK\System\Polyfill\ScriptAssocArrayAccessTrait::class,
                    IGK\System\Configuration\ConfigArrayAccessTrait::class,
                    IGK\Controllers\ControllerUriTrait::class,
                    IGK\System\Polyfill\JsonSerializableTrait::class,
                    IGK\System\IO\FileSystem::class,
                    IGKIterator::class,
                    IGKUserInfo::class,
                    IIGKArrayObject::class,
                    IGKResourceUriResolver::class,
                    // \IGK\Database\SQLDataAdapter::class,
                ]);
                // vs 
                // require_once IGK_LIB_CLASSES_DIR . "/System/Polyfill/ArrayAccessSelfTrait.7.php";
                // require_once IGK_LIB_CLASSES_DIR . "/System/Polyfill/CSSDefaultArrayAccess.7.php";
                // require_once IGK_LIB_CLASSES_DIR . "/System/Polyfill/IGKMediaArrayAccessTrait.7.php";
                // require_once IGK_LIB_CLASSES_DIR . "/System/Polyfill/IteratorTrait.7.php";
                // require_once IGK_LIB_CLASSES_DIR . "/System/Polyfill/ScriptAssocArrayAccessTrait.7.php";
                // require_once IGK_LIB_CLASSES_DIR . "/System/Configuration/ConfigArrayAccessTrait.7.php";
                // require_once IGK_LIB_CLASSES_DIR . "/Controllers/ControllerUriTrait.php";
                // require_once IGK_LIB_CLASSES_DIR . "/System/IO/FileSystem.7.php";
                // require_once IGK_LIB_CLASSES_DIR . "/IGKIterator.php";
                // require_once IGK_LIB_CLASSES_DIR . "/IGKUserInfo.php";
                // require_once IGK_LIB_CLASSES_DIR . "/IIGKArrayObject.php";
                // require_once IGK_LIB_CLASSES_DIR . "/IGKResourceUriResolver.php"; 
                // // + |  Load required files    
            }

            self::$sm_instance->_coreload = true;
            require_once IGK_LIB_CLASSES_DIR . "/IGKApp.php";
            require_once IGK_LIB_CLASSES_DIR . "/IGKLibraryBase.php";
            require_once IGK_LIB_CLASSES_DIR . "/Models/ModelBase.php";
            require_once IGK_LIB_CLASSES_DIR . "/Database/DbQueryDriver.php";
            require_once IGK_LIB_CLASSES_DIR . "/Database/SQLDataAdapter.php";
            require_once IGK_LIB_CLASSES_DIR . "/System/Configuration/Controllers/ConfigControllerBase.php";
            require_once IGK_LIB_CLASSES_DIR . "/System/Configuration/Controllers/ConfigControllerRegistry.php";
            require_once IGK_LIB_CLASSES_DIR . "/System/Diagnostics/Benchmark.php";
        }
        //return null;
        $app = IGKApplicationFactory::Create($type);
        // igk_wln(__FILE__.":".__LINE__, "dummy");
        $app->bootstrap();
        if ($boot) {
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
            if (defined('IGK_SESS_DIR') && IO::CreateDir(IGK_SESS_DIR)) {
                ini_set("session.save_path", IGK_SESS_DIR);
            } 
            self::$sm_instance->path = IGKPath::getInstance();
            $package_dir = self::$sm_instance->path->getPackagesDir();
            // + | -----------------------------------------------------
            // + | Autoloading composer packages
            // + | 
            if (is_file($package_dir . "/composer.json") && is_file($package_dir . "/vendor/autoload.php")) {
                // preload spl loading class
                spl_autoload_unregister($initialize["spl_auto_loader"]);
                require_once($package_dir . "/vendor/autoload.php");
                spl_autoload_register($initialize["spl_auto_loader"], true, true);
            }
            igk_hook(IGKEvents::HOOK_APP_BOOT, [$app]);
        }
        // + | -----------------------------------------------------
        // + | return the application 
        // + |  
        return $app;
    }
}
