<?php

require_once __DIR__ . "/interfaces.php";
require_once __DIR__ . "/Helper/StringUtility.php";
require_once __DIR__ . "/Helper/IO.php";
require_once __DIR__ . "/System/IO/Path.php";
require_once __DIR__ . "/System/IO/FileWriter.php";
require_once __DIR__ . "/IGKObject.php";
require_once __DIR__ . "/IGKServer.php";

require_once __DIR__ . "/Cache/CommonCache.php";
require_once __DIR__ . "/Controllers/RootControllerBase.php";
// require_once __DIR__."/System/Html/Dom/HtmlItemAttribute.php";

use IGK\Helper\IO;
use IGK\System\IO\Path as IGKPath;
use IGK\helper\StringUtility;



///<summary>core application loader </summary>
class IGKApplicationLoader
{

    private static $sm_instance;

    /**
     * @return IGKApplicationLoader instance
     */
    public static function getInstance()
    {
        return self::$sm_instance;
    }

    /**
     * path manager handler
     * @var mixed
     */
    private $path;


    /**
     * load callable
     */
    private $callables = [];

    private function __constrct()
    {
    }
    /**
     * register autoload callback
     * @param mixed $callable 
     * @return void 
     */
    private function Load($callable, $priority = 20, $classdir=null, $namespace=null)
    {
        $this->callables[] = get_defined_vars(); 
        $this->sorted = 1;
    }
    public function registerLoading($entryNS, $classdir, $priority = 20, &$refile = null)
    {
        
        $cl = & igk_environment()->createArray(IGKEnvironment::AUTO_LOAD_CLASS);
        if (!isset($cl[$classdir])) {
            $cl[$classdir] = compact("entryNS", "refile");
            $this->Load(function ($n) use ($classdir, & $cl) {           
                $e_ns = igk_getv($cl[$classdir], "entryNS");                
                if (!empty($e_ns) && (strpos($n, $e_ns."\\")===0))
                {
                    $n = substr($n, strlen($e_ns)+1); 
                }
                $g = self::_TryLoadClasses([$n], $classdir, false);
                return $g;
            }, $priority, $classdir, $entryNS);
            // igk_environment()->set($key, $cl);
        }
    }

    private function _sort_priority($a, $b)
    {
        $g = strcmp($b["namespace"], $a["namespace"]);
        if ($g != 0){
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
                if(!is_dir($c["classdir"]) || (!empty($ns = $c["namespace"]) && (strpos($n, ltrim($ns."\\", "\\"))!==0))){
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
        return self::_TryLoadClasses($classnames, IGK_LIB_CLASSES_DIR, true);
    }

    private static function _TryLoadClasses(array $classnames, $path, $throw = false)
    {

        list($major, $minor) = explode(".", PHP_VERSION);
        $resolv_class =  [$major . "." . $minor, $major, ""];
        $cdir = null;
        $is_core  = IGK_LIB_CLASSES_DIR == $path;
        $result = true;
        if (!is_array($path)){
            $path = [$path];            
        }
        while($cdir = array_shift($path)){
            if (!is_dir($cdir)){
                continue;
            }
            while ($result &&  ($classname = array_shift($classnames)) !== null) {
                
                
                // load class method
                if (!class_exists($classname, false) && !trait_exists($classname, false) && !interface_exists($classname, false)) {
                    // igk_ilog("tryload:".$classname);
                    $n = $classname;
                    $f = StringUtility::Uri($n);
                    if ((strpos($f, "IGK/") === 0) && $is_core) {
                        $f = substr($f, 4);
                    }
                    $found = false;
                    foreach ($resolv_class as $version) {
                        $ext = (!empty($version) ? ".{$version}" : "") . ".php";
                        if (
                            file_exists($cf = ($cdir . "/" . $f . $ext)) ||
                            (!empty($version) && file_exists($cf = ($cdir . "/{$version}/" . $f . ".php")))
                        ) {
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
                            // first version file founded
                            $found = true;
                            break;
                        }
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
        $srv = IGKServer::getInstance();
        // + | Initialize environment 
        self::$sm_instance =  new self();
        $v_loader = self::$sm_instance;
        
        spl_autoload_register([$v_loader, '_auto_load']); 

        $app = IGKApplicationFactory::Create($type);

        $app->bootstrap();

        // + |-----------------------------------------------------------------------
        // + | mandatory constants
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
            define("IGK_MODULE_DIR", IGK_APP_DIR . "/" . IGK_MODULE_FOLDER);
        }
        if (defined('IGK_SESS_DIR') && IO::CreateDir(IGK_SESS_DIR)) {
            ini_set("session.save_path", IGK_SESS_DIR);
        }
    
        $v_loader->path = IGKPath::getInstance();

        $package_dir = $v_loader->path->getPackagesDir();
        // + | -----------------------------------------------------
        // + | Autoloading composer packages
        // + |
        if (file_exists( $package_dir."/composer.json")){
            require_once( $package_dir."/vendor/autoload.php");
        } 
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
            IGK\System\IO\FileSystem::class,
            IGKIterator::class,
            IGKUserInfo::class,
            IIGKArrayObject::class,
            IGKResourceUriResolver::class,
        ]); 
        // + |  Load required files 
        require_once IGK_LIB_CLASSES_DIR . "/IGKApplicationBase.php";
        require_once IGK_LIB_CLASSES_DIR . "/IGKApplicationFactory.php";
        require_once IGK_LIB_CLASSES_DIR . "/IGKWebApplication.php";
        require_once IGK_LIB_CLASSES_DIR . "/IGKApp.php";
        require_once IGK_LIB_CLASSES_DIR . "/IGKLibraryBase.php"; 
        // + | -----------------------------------------------------
        // + | return the application 
        // + |
        // igk_wln_e("time: ".IGKServer::RequestTime());
        
        return $app;
    }
}
