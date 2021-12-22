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
    public function Load($callable, $priority = 20)
    {
        $this->callables[] = compact("callable", "priority");
        $this->sorted = 1;
    }
    public function registerLoading($entryNS, $classdir, $priority = 20, &$refile = null)
    {
        $key = IGKEnvironment::AUTO_LOAD_CLASS;
        $cl = &igk_environment()->createArray($key);
        if (!isset($cl[$classdir])) {
            $this->Load(function ($n) use ($classdir, &$refile) {
                $g = self::_TryLoadClasses([$n], $classdir, false);
                return $g;
            }, $priority);
            $cl[$classdir] = compact("entryNS", "refile");
            // igk_environment()->set($key, $cl);
        }
    }

    private function _sort_priority($a, $b)
    {
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
        $cdir = $path;
        $is_core  = IGK_LIB_CLASSES_DIR == $path;
        $result = true;
        while ($result &&  ($classname = array_shift($classnames)) !== null) {
            // echo "load : ".$classname."<br />\n";
            if ($classname == "IGK\System\Html\Dom\HtmlNoTagNode") {
                echo (".......................<br />");
            }
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

        $bdir = defined("IGK_BASE_DIR") ? IGK_BASE_DIR : getcwd();
        // -----------------------------------------------------------------------
        // + mandatory constants
        // -----------------------------------------------------------------------
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
        /**
         * allow auto loading 
         */
        if (file_exists( $package_dir."/composer.json")){
            require_once( $package_dir."/vendor/autoload.php");
        } 


        // + | Load traits according to version

        self::LoadClasses([
            IGK\System\Polyfill\ArrayAccessSelfTrait::class,
            IGK\System\Polyfill\CSSDefaultArrayAccess::class,
            IGK\System\Polyfill\IGKMediaArrayAccessTrait::class,
            IGK\System\Polyfill\IteratorTrait::class,
            IGK\System\Polyfill\ScriptAssocArrayAccessTrait::class,
            IGK\System\Configuration\ConfigArrayAccessTrait::class,
            IGK\Controllers\ControllerUriTrait::class,
            IGKEnvironment::class,
            IGK\System\IO\FileSystem::class,
            IGKIterator::class,
            IGKUserInfo::class,
            IIGKArrayObject::class,
            IGKResourceUriResolver::class,
        ]);
        // echo "<pre>";
        // print_r(get_included_files());
        // echo "</pre>";
        // // igk_wln_e("tkjd");
        // exit;

        // + |  Load required files
        require_once IGK_LIB_CLASSES_DIR . "/IGKEnvironment.php";
        require_once IGK_LIB_DIR . "/Lib/Classes/IGKApplicationBase.php";
        require_once IGK_LIB_DIR . "/Lib/Classes/IGKApplicationFactory.php";
        require_once IGK_LIB_DIR . "/Lib/Classes/IGKWebApplication.php";
        require_once IGK_LIB_CLASSES_DIR . "/IGKApp.php";
        require_once IGK_LIB_DIR . "/Lib/Classes/IGKLibraryBase.php";


        // init environment  

        // require_once IGK_LIB_DIR."/Lib/Classes/IGKIO.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKObject.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKObject.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKString.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKException.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/Resources/R.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/Resources/IGKLangKey.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/Resources/IGKLangResDictionary.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/HtmlUtils.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKEnvironment.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKApp.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKAppSetting.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKAppConfig.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKValidator.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/System/Configuration/ConfigUtils.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/System/Configuration/ConfigData.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKSession.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKEvents.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/Controllers/RootControllerBase.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/Controllers/BaseController.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/Controllers/ControllerExtension.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/System/Http/RequestHandler.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKSubDomainManager.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKControllerManagerObject.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/System/Helper.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/System/IO/File/PHPScriptBuilderUtility.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/System/Configuration/ControllerConfigurationData.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/System/IO/FileWriter.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKPageView.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKFv.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKObjectGetProperties.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKHtmlScriptManager.php"; 
        // require_once IGK_LIB_DIR."/Lib/Classes/HtmlDocThemeMediaType.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKRawDataBinding.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKValueListener.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKNotifyStorage.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKCaches.php"; 
        // require_once IGK_LIB_DIR."/Lib/Classes/System/IO/FileSystem.php";

        // require_once IGK_LIB_DIR."/Lib/Classes/IGKOwnViewCtrl.php";

        // require_once IGK_LIB_DIR."/Lib/Classes/IGKOb.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKServerInfo.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKResourceUriResolver.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKHtmlMetaManager.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKHtmlMetaManager.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKHtmlBodyMainScript.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKLog.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKUserAgent.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKDataAdapter.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKUserInfo.php";
        // //+ |
        // //+ | configuration controllers
        // //+ |
        // require_once IGK_LIB_DIR."/Lib/Classes/System/Configuration/Controllers/ConfigControllerBase.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/System/Configuration/Controllers/IGKConfigCtrl.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/System/Configuration/Controllers/IGKMenuCtrl.php"; 
        // require_once IGK_LIB_DIR."/Lib/Classes/System/Configuration/Controllers/IGKSystemUriActionCtrl.php";         
        // require_once IGK_LIB_DIR."/Lib/Classes/System/Configuration/Controllers/IGKPicResController.php";


        // //+ | primary web node 

        // require_once IGK_LIB_DIR."/Lib/Classes/System/Html/Dom/Factory.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/System/Html/IGKHtmlScriptLink.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/System/Html/IGKHtmlCallable.php";


        // require_once IGK_LIB_DIR."/Lib/Classes/IGKMedia.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/System/Html/Dom/HtmlItemBase.php"; 
        // require_once IGK_LIB_DIR."/Lib/Classes/System/Html/Dom/HtmlNoTagNode.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKHtmlChildElementCollections.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKXmlChilds.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKHtmlAHref.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKHtmlStyleValueAttribute.php";


        // require_once IGK_LIB_DIR."/Lib/Classes/IGKHtmlContext.php"; 
        // require_once IGK_LIB_DIR."/Lib/Classes/HtmlItemAttribute.php"; 
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKHtmlAttribs.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/HtmlCssClassValueAttribute.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKHtmlScriptAssocInfo.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKHtmlOptions.php"; 
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKHtmlRelativeUriValueAttribute.php";


        // require_once IGK_LIB_DIR."/Lib/Classes/System/Http/RequestResponse.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/System/Http/WebResponse.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/System/Http/Request.php";

        // // on css context loading 
        // require_once IGK_LIB_DIR."/Lib/Classes/Css/IGKCssContext.php";



        // // + | Controllers
        // require_once IGK_LIB_DIR."/Lib/Classes/Controllers/IGKNonVisibleControllerBase.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/Controllers/IGKComponentManagerCtrl.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/Controllers/IGKDebugCtrl.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/Controllers/IGKSharedContentHtmlItemCtrl.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/Controllers/IGKSystemController.php"; 
        // require_once IGK_LIB_DIR."/Lib/Classes/Controllers/IGKPaletteController.php";   
        // require_once IGK_LIB_DIR."/Lib/Classes/ControllerTypeBase.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/Controllers/IGKPageControllerBase.php"; 
        // require_once IGK_LIB_DIR."/Lib/Classes/ApplicationController.php"; 



        // require_once IGK_LIB_DIR."/Lib/Classes/IGKXMLNodeType.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKReaderBindingInfo.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/HtmlReader.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/HtmlReaderDocument.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/DbQueryDriver.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/IGKQueryResult.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/System/Database/SQLGrammar.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/System/Database/NoDbConnection.php";
        // require_once IGK_LIB_DIR."/Lib/Classes/System/Installers/IGKBalafonInstaller.php";

        // require_once IGK_LIB_DIR."/igk_html_func_items.php";
        // require_once IGK_LIB_DIR."/igk_request_handle.php";
        // require_once IGK_PROJECT_DIR."/igk_default/class.igk_default.php";

        // class_alias(IGK\System\Html\Dom\IGKHtmlDiv::class, "IGKHtmlDiv");
        // create application depending of application type 
       

        // $app->libary('session');
        // $app->libary('mysql');
        // return the application 
        return $app;
    }
}
