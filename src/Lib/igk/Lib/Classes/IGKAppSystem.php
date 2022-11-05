<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKAppSystem.php
// @date: 20220803 13:48:54
// @desc: 

use IGK\ApplicationLoader;
use IGK\Helper\IO;
use IGK\Helper\StringUtility;
use IGK\System\IO\Path;
use IGK\Cache\SystemFileCache as IGKSysCache;
use IGK\System\Caches\EnvControllerCacheDataBase;
use IGK\System\Caches\InitEnvControllerChain;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\Controllers\BaseController;
use IGK\Controllers\SessionController;
use IGK\Controllers\SysDbController;
use IGK\System\Caches\EnvControllerCacheList;

///<summary> manage application system</summary>
class IGKAppSystem
{

    /**
     * init application environment
     * @param string $dirname 
     * @param IGKApp $app 
     * @return int|void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function InitEnv(string $dirname, IGKApp $app)
    {

        if (!is_dir($dirname))
            return -9;
        if (!defined("IGK_APP_DIR"))
            define("IGK_APP_DIR", $dirname);
        if (defined("IGK_INIT") && IGK_INIT) {
            return;
        }
        $path = Path::getInstance();
        $project_dir = igk_io_projectdir();
        $app_dir = igk_io_applicationdir();
        $confFILE = StringUtility::UriCombine($path->getDataDir(), "configure");

        /// TODO: force init

        if (!(defined('IGK_INIT') && IGK_INIT) && file_exists($confFILE)) {
            foreach ([igk_io_cachedir(), igk_io_basedir() . "/" . IGK_RES_FOLDER] as $cdir) {
                !is_dir($cdir) && IO::Createdir($cdir);
            }
            // + | -----------------------------------------
            // + | expected load lib cache for max 5ms
            // + | 
            self::LoadEnvironment($app);
            !defined('IGK_INIT') && define('IGK_INIT', 1);
            return;
        }

        // + | ------------------------------------------------------------------
        // + | check if can open directory name 
        $hdir = null;
        if (!is_dir($dirname) || !($hdir = opendir($dirname)))
            return;
        closedir($hdir);


        igk_environment()->set(IGKEnvironment::INIT_APP, 1);


        $idx = $path->getBaseDir() . "/index.php";
        if (!file_exists($idx)) {
            $indexsrc = igk_getbaseindex_src(IGK_LIB_FILE);
            igk_io_save_file_as_utf8($idx, $indexsrc);
        }


        $ips = igk_server_name();
        self::InstallDir($idx, $app_dir, $dirname, $project_dir, $path->getDataDir(), $path->getSysDataDir(), [
            "domain_name" => !IGKValidator::IsIPAddress($ips) ? $ips : IGK_DOMAIN,
        ]);
        igk_raise_initenv_callback();
        igk_environment()->set(IGKEnvironment::INIT_APP, null);
        igk_reg_hook(IGKEvents::HOOK_BEFORE_INIT_APP, [self::class, "reloadConfigCallback"]);
    }
    public static function reloadConfigCallback()
    {
        igk_app()->getConfigs()->reload();
        igk_unreg_hook(IGKEvents::HOOK_BEFORE_INIT_APP, [self::class, __FUNCTION__]);
    }

    public static function InstallDir(
        string $idx,
        string $app_dir,
        string $dirname,
        string $project_dir,
        string $data_dir,
        string $sys_datadir,
        ?array $options = null
    ) {
        $access = "deny from all";
        $old = umask(0);
        $is_primary = ($app_dir == $dirname);
        $v_access = dirname($idx) . "/.htaccess";
        if (!file_exists($v_access)) {

            igk_io_save_file_as_utf8($v_access, igk_getbase_access(
                $dirname
            ), true);
        }
        $confFILE = StringUtility::UriCombine($data_dir, "configure");
        igk_io_save_file_as_utf8($app_dir . "/Lib/.htaccess", $access, true);
        IO::CreateDir($dirname . "/" . IGK_RES_FOLDER);
        igk_io_save_file_as_utf8($dirname . "/" . IGK_RES_FOLDER . "/.htaccess", "allow from localhost", true);
        IO::CreateDir($dirname . "/" . IGK_RES_FOLDER . "/Img");
        igk_io_save_file_as_utf8($dirname . "/" . IGK_RES_FOLDER . "/" . IGK_STYLE_FOLDER . "/.htaccess", "allow from all", true);
        IO::CreateDir($dirname . "/" . IGK_RES_FOLDER . "/Layouts");
        igk_io_save_file_as_utf8($dirname . "/" . IGK_RES_FOLDER . "/" . IGK_STYLE_FOLDER . "/.htaccess", "allow from all", true);
        IO::CreateDir($dirname . "/" . IGK_RES_FOLDER . "/Styles");
        igk_io_save_file_as_utf8($dirname . "/" . IGK_RES_FOLDER . "/" . IGK_STYLE_FOLDER . "/.htaccess", "allow from all", true);
        IO::CreateDir($dirname . "/" . IGK_RES_FOLDER . "/Fonts");
        igk_io_save_file_as_utf8($dirname . "/" . IGK_RES_FOLDER . "/Fonts/.htaccess", "allow from all", false);
        IO::CreateDir($dirname . "/" . IGK_RES_FOLDER . "/Videos");
        igk_io_save_file_as_utf8($dirname . "/" . IGK_RES_FOLDER . "/" . IGK_STYLE_FOLDER . "/ie.css", "@import url(\"base.css\");", true);
        igk_io_save_file_as_utf8($dirname . "/" . IGK_RES_FOLDER . "/" . IGK_STYLE_FOLDER . "/mod.css", "@import url(\"base.css\");", true);
        igk_io_save_file_as_utf8($dirname . "/" . IGK_RES_FOLDER . "/" . IGK_STYLE_FOLDER . "/base.css", igk_css_get_default_style(), true);
        IO::CreateDir($dirname . "/" . IGK_RES_FOLDER . "/Themes");
        $theme = IGK_DEFAULT_THEME_FOLDER . "/default.theme";
        $v_f = IO::ReadAllText($theme);
        if (!empty($v_f)) {
            igk_io_save_file_as_utf8($dirname . "/" . IGK_RES_FOLDER . "/Themes/default.theme", $v_f, false);
        }
        IO::CreateDir($project_dir);
        IO::CreateDir($app_dir . "/" . IGK_PACKAGES_FOLDER);
        igk_io_save_file_as_utf8($app_dir . "/" . IGK_PACKAGES_FOLDER . "/.htaccess", $access, false);
        igk_io_save_file_as_utf8($project_dir . "/.htaccess", $access, true);

        IO::CreateDir($data_dir);
        IO::CreateDir($data_dir . "/Lang");
        if (is_dir($v_dir = $sys_datadir)) {
            IO::CopyFiles($v_dir, $data_dir, true);
        }
        igk_io_save_file_as_utf8($app_dir . "/" . IGK_DATA_FOLDER . "/.htaccess", $access, false);
        igk_io_save_file_as_utf8($app_dir . "/" . IGK_CONF_DATA, igk_get_defaultconfigdata(), false);
        IO::CreateDir($app_dir . "/" . IGK_SCRIPT_FOLDER);
        IO::CreateDir($app_dir . "/" . IGK_INC_FOLDER);
        igk_io_save_file_as_utf8($app_dir . "/" . IGK_INC_FOLDER . "/.htaccess", "deny from all");
        IO::CreateDir($app_dir . "/" . IGK_CACHE_FOLDER, 0775);
        igk_io_save_file_as_utf8($app_dir . "/" . IGK_CACHE_FOLDER . "/.htaccess", "deny from all");

        // + init cgi-bin 
        IO::CreateDir($app_dir . "/cgi-bin");
        igk_io_save_file_as_utf8($app_dir . "/cgi-bin/.htaccess", "deny from all");
        igk_io_save_file_as_utf8($app_dir . "/cgi-bin/cronjob.php", igk_get_defaultcron_data(), false);
        // 
        // load library folder  
        //
        self::_LoadEnvFiles();
        igk_io_save_file_as_utf8($confFILE, "1", false);
        igk_io_save_file_as_utf8($data_dir . "/domain.conf", igk_getv($options, "domain_name"), true);
        $cgi = IGK_LIB_DIR . "/cgi-bin";
        if (!igk_phar_running() && ($ctab = igk_io_getfiles($cgi, "/\.cgi$/"))) {
            foreach ($ctab as $k) {
                @chmod($k, octdec("0755"));
            }
        }
        if ($is_primary) {
            igk_io_save_file_as_utf8($app_dir . "/" . IGK_CONF_FOLDER . "/index.php", igk_config_php_index_content(), false);
            igk_io_save_file_as_utf8($app_dir . "/" . IGK_CONF_FOLDER . "/.htaccess", igk_getconfig_access(), false);
        }
        umask($old);
    }
    /**
     * load environement files
     * @return array 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    private static function _LoadEnvFiles()
    {
        return igk_load_env_files(IGK_LIB_DIR, array("Inc", "Ext", "SysMods", igk_io_projectdir()));
    }
    /**
     * load environment and cache
     * @return void 
     * @throws IGKException 
     */
    public static function LoadEnvironment(IGKApp $app)
    {
        if (!IGKSysCache::LoadCacheLibFiles()) {
            \IGK\System\Diagnostics\Benchmark::mark("loadlib_cache");
            $t_files = self::_LoadEnvFiles();
            igk_reglib($t_files);
            IGKSysCache::CacheLibFiles(true);
            \IGK\System\Diagnostics\Benchmark::expect("loadlib_cache", 2.00);
        }
        self::_InitControllerEnvironment($app);
    }
    /**
     * init controller environment
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    private static function _InitControllerEnvironment(IGKApp $app)
    {
        // + | --------------------------------------------------------------------
        // + | INIT CONTROLLER ENVIRONMENT
        // + |

        $manager = $app->getControllerManager();
        $loader = ApplicationLoader::getInstance();
        $c = new InitEnvControllerChain;

        // + | --------------------------------------------------------------------
        // + | LOAD DB SCHEMA CACHE
        // + |

        if (!is_file($file = igk_io_cachedir() . "/" . EnvControllerCacheDataBase::FILE)){
            $sysdb = igk_getctrl(SysDbController::class);
            $c->add(new EnvControllerCacheDataBase($file, $sysdb));
        }

        // + | --------------------------------------------------------------------
        // + | LOAD CONTROLLER LISTS
        // + |
        $tab =  EnvControllerCacheList::GetControllersClasses();        
        foreach ($tab as $cl) {
            if (is_subclass_of($cl, BaseController::class)) {
                // register controller
                $g = igk_sys_reflect_class($cl);
                if ($g->isAbstract() || !$g->getConstructor()->isPublic()) {
                    continue;
                }
                $o = new $cl();
                $manager->register($o);
                $rfile = $g->getFileName();
                $loader->registerClass(
                    $rfile,
                    $cl,
                    ""
                );
                $c->update($o);
            }
        }
        $c->complete();
    }
    private function __construct()
    {
    }
}
