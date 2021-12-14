<?php

use IGK\Helper\IO;
use IGK\Helper\StringUtility;
use IGK\System\IO\Path;
use IGK\Cache\SystemFileCache as IGKSysCache;

///<summary> manage application system</summary>
class IGKAppSystem{

    public static function InitEnv(string $dirname){
      
        header("Content-Type: text/html"); 
        if(!is_dir($dirname))
            return -9;
        $Rfile=$dirname."/".IGK_RES_FOLDER."/R.class.php";
        if(!defined("IGK_APP_DIR"))
            define("IGK_APP_DIR", $dirname);
        if(defined("IGK_INIT") && IGK_INIT){
            return; 
        }
        if(defined('IGK_SESS_DIR') && IO::CreateDir(IGK_SESS_DIR)){
            ini_set("session.save_path", IGK_SESS_DIR);
        }
        $path = Path::getInstance();
        $project_dir=igk_io_projectdir();
        $app_dir=igk_io_applicationdir();
        $confFILE = StringUtility::UriCombine($path->getDataDir(), "configure");
        /// TODO: force init
    
        if(!(defined('IGK_INIT') && IGK_INIT) && file_exists($confFILE)){
            foreach([igk_io_cachedir(), igk_io_basedir()."/".IGK_RES_FOLDER] as $cdir){
                !is_dir($cdir) && IO::Createdir($cdir);
            }
            if(file_exists($Rfile))
                include_once($Rfile);
            if(1 || !IGKSysCache::LoadCacheLibFiles()){
                $t_files=igk_load_env_files(IGK_LIB_DIR, array("Inc", "Ext", igk_io_projectdir()));
                // $t_files=igk_load_env_files($dirname, array(igk_io_projectdir()));
                igk_reglib($t_files);
                IGKSysCache::CacheLibFiles(true); 
            }
            IGKSubDomainManager::Init();
            !defined('IGK_INIT') && define('IGK_INIT', 1);
            return;
        }
        $hdir=null;
        if(!is_dir($dirname) || !($hdir=opendir($dirname)))
            return;

        igk_environment()->set(IGKEnvironment::INIT_APP, 1);       
        $access="deny from all";
        $old=umask(0);
        $idx= $path->getBaseDir()."/index.php";
        $v_access= $path->getBaseDir()."/.htaccess";
        if(!file_exists($idx)){
            $indexsrc=igk_getbaseindex_src(__FILE__);
            igk_io_save_file_as_utf8($idx, $indexsrc);
        }
        if(!file_exists($v_access)){
            igk_io_save_file_as_utf8($v_access, igk_getbase_access(), true);
        }
        igk_io_save_file_as_utf8($app_dir."/Lib/.htaccess", $access, true);
        IO::CreateDir($dirname."/".IGK_RES_FOLDER);
        igk_io_save_file_as_utf8($dirname."/".IGK_RES_FOLDER."/.htaccess", "allow from localhost", true);
        IO::CreateDir($dirname."/".IGK_RES_FOLDER."/Img");
        igk_io_save_file_as_utf8($dirname."/".IGK_RES_FOLDER."/".IGK_STYLE_FOLDER."/.htaccess", "allow from all", true);
        IO::CreateDir($dirname."/".IGK_RES_FOLDER."/Layouts");
        igk_io_save_file_as_utf8($dirname."/".IGK_RES_FOLDER."/".IGK_STYLE_FOLDER."/.htaccess", "allow from all", true);
        IO::CreateDir($dirname."/".IGK_RES_FOLDER."/Styles");
        igk_io_save_file_as_utf8($dirname."/".IGK_RES_FOLDER."/".IGK_STYLE_FOLDER."/.htaccess", "allow from all", true);
        IO::CreateDir($dirname."/".IGK_RES_FOLDER."/Fonts");
        igk_io_save_file_as_utf8($dirname."/".IGK_RES_FOLDER."/Fonts/.htaccess", "allow from all", false);
        IO::CreateDir($dirname."/".IGK_RES_FOLDER."/Videos");
        igk_io_save_file_as_utf8($dirname."/".IGK_RES_FOLDER."/".IGK_STYLE_FOLDER."/ie.css", "@import url(\"base.css\");", true);
        igk_io_save_file_as_utf8($dirname."/".IGK_RES_FOLDER."/".IGK_STYLE_FOLDER."/mod.css", "@import url(\"base.css\");", true);
        igk_io_save_file_as_utf8($dirname."/".IGK_RES_FOLDER."/".IGK_STYLE_FOLDER."/base.css", igk_css_getdefaultstyle(), true);
        IO::CreateDir($dirname."/".IGK_RES_FOLDER."/Themes");
        $theme=IGK_DEFAULT_THEME_FOLDER."/default.theme";
        $v_f=IO::ReadAllText($theme);
        if(!empty($v_f)){
            igk_io_save_file_as_utf8($dirname."/".IGK_RES_FOLDER."/Themes/default.theme", $v_f, false);
        }
        IO::CreateDir($project_dir);
        IO::CreateDir($app_dir."/".IGK_PACKAGES_FOLDER);
        igk_io_save_file_as_utf8($app_dir."/".IGK_PACKAGES_FOLDER."/.htaccess", $access, false);
        igk_io_save_file_as_utf8($project_dir."/.htaccess", $access, true);
        $data_dir=$app_dir."/".IGK_DATA_FOLDER;
        IO::CreateDir($data_dir);
        IO::CreateDir($data_dir."/Lang");
        if (is_dir($v_dir = $path->getSysDataDir())){
            IO::CopyFiles($v_dir, $path->getDataDir(), true);
        }
        igk_io_save_file_as_utf8($app_dir. "/".IGK_DATA_FOLDER."/.htaccess", $access, false);
        igk_io_save_file_as_utf8($app_dir. "/".IGK_CONF_DATA, igk_get_defaultconfigdata(), false);
        IO::CreateDir($app_dir. "/".IGK_SCRIPT_FOLDER);
        IO::CreateDir($app_dir. "/".IGK_INC_FOLDER);
        igk_io_save_file_as_utf8($app_dir. "/".IGK_INC_FOLDER."/.htaccess", "deny from all");
        IO::CreateDir($app_dir."/".IGK_CACHE_FOLDER, 0775);
        igk_io_save_file_as_utf8($app_dir. "/".IGK_CACHE_FOLDER."/.htaccess", "deny from all");
        // load library folder 
        igk_load_env_files(IGK_LIB_DIR);
        igk_loadcontroller($app_dir. "/".IGK_INC_FOLDER);
        igk_loadcontroller($project_dir);
        igk_io_save_file_as_utf8($confFILE, "1", false);
        $ips=igk_server_name();
        igk_io_save_file_as_utf8($path->getDataDir()."/domain.conf", !IGKValidator::IsIPAddress($ips) ? $ips: IGK_DOMAIN, true);
        $cgi=IGK_LIB_DIR."/cgi-bin";
        if(!igk_phar_running() && ($ctab=igk_io_getfiles($cgi, "/\.cgi$/"))){
            foreach($ctab as $k){
                @chmod($k, octdec("0755"));
            }
        }
        igk_io_save_file_as_utf8($app_dir. "/".IGK_CONF_FOLDER."/index.php", igk_config_php_index_content(), false);
        igk_io_save_file_as_utf8($app_dir. "/".IGK_CONF_FOLDER."/.htaccess", igk_getconfig_access(), false);
        closedir($hdir);
        umask($old);
        igk_raise_initenv_callback();
        igk_environment()->set(IGKEnvironment::INIT_APP, null);
        IGKSubDomainManager::Init();
    }
    private function __construct()
    {
        
    }
}