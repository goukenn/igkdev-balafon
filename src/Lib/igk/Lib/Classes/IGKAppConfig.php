<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKAppConfig.php
// @date: 20220803 13:48:54
// @desc: 
use IGK\System\Configuration\ConfigData;
use IGK\System\IO\FileSystem;
use function igk_resources_gets as __; 
/**
* Represent IGKAppConfig class
* @property \IGK\System\Configuration\ConfigData $Data get property data
* @property bool $BootStrap
* @property bool $BootStrap.Enabled
* @property bool $JQuery.Enabled
* @property bool $admin_login
* @property bool $admin_pwd
* @property bool $allow_article_config
* @property bool $allow_auto_cache_page
* @property bool $allow_debugging
* @property bool $allow_log
* @property bool $allow_page_cache
* @property bool $app_default_controller_tag_name
* @property bool $cache_file_time
* @property bool $cache_loaded_file
* @property bool $company_name
* @property bool $configuration_port
* @property bool $copyright
* @property bool $date_time_zone default time zone 
* @property bool $datetime_format default time format
* @property bool $db_auto_create check to create database 
* @property bool $db_default_column_id default column identifier id
* @property bool $db_driver the db driver
* @property bool $db_name   the global database name
* @property bool $db_port   the global database port for mysql
* @property bool $db_prefix the global table prefix
* @property bool $db_pwd    the global database password
* @property bool $db_server the global database server for adapter that require server connection 
* @property bool $db_user   the global database server user for adapter that require server connection
* @property bool $default_author default script author name
* @property bool $default_controller set default controller
* @property bool $default_dataadapter set default dataadapter
* @property bool $default_lang set default lang
* @property bool $default_user set the default configuration user to get 
* @property bool $display_errors
* @property bool $error_debug
* @property bool $error_reporting
* @property bool $force_secure_redirection
* @property bool $force_single_controller_app
* @property bool $globaltheme
* @property bool $help_uri
* @property bool $informAccessConnection
* @property bool $mail_admin
* @property bool $mail_authtype
* @property bool $mail_contact
* @property bool $mail_noreply
* @property bool $mail_password
* @property bool $mail_port
* @property bool $mail_portal
* @property bool $mail_server
* @property bool $mail_testmail
* @property bool $mail_useauth
* @property bool $mail_user
* @property bool $max_script_execution_time
* @property bool $menuHostCtl
* @property bool $menu_defaultPage
* @property bool $meta_copyright
* @property bool $meta_description
* @property bool $meta_enctype
* @property bool $meta_keywords
* @property bool $meta_title
* @property bool $ob_buffer_padding_length
* @property bool $ovh
* @property bool $php_run_script
* @property bool $phpmyadmin_uri
* @property bool $powered_messae
* @property bool $powered_message
* @property bool $powered_uri
* @property bool $python_run_script
* @property bool $secure_port
* @property bool $show_debug
* @property bool $show_powered
* @property bool $site_dir
* @property bool $sitemap_xsl
* @property bool $support_lang
* @property bool $website_adminmail
* @property bool $website_domain
* @property string $website_prefix
* @property string $website_title
* @property string $doc_nocache
*/
final class IGKAppConfig extends IGKObject {

    /**
    * auto generate doc.
    * @var mixed
    */
    const CHANGE_REG_KEY="IGKConfigDataChanged";

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_configEntries;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_configSavedEvent;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_datas;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_oldState;
    /** @var IGKAppConfig */
    private static $sm_instance;
    /**
    * 
    */
    private function __construct(){
        $this->_loadSystemConfig();
    }
    /**
    * load core configuration files
    */
    private function _loadSystemConfig(){
        $file=IGK_CONF_DATA;
        $this->m_configEntries=array();
        $extra = [];
        $fullpath = null;
        $b = igk_io_basenamewithoutext($file);
        $dir = dirname(igk_io_syspath($file));
        $v_load = false;
        foreach(["", ".".igk_environment()->name()] as $f){
            $n = $dir."/".$b.$f.".php"; 
            if (FileSystem::Exists($n)){        
                $fullpath=$n; 
                IGK\System\Configuration\ConfigUtils::LoadData($fullpath, $this->m_configEntries, true, empty($f));      
                $v_load = true; 
            } 
        }
        if (!$v_load){
           $this->m_configEntries = array_merge([],include(IGK_LIB_DIR."/.setting.global.pinc"));
        }
        // + | load extra configuration files
        $preload_configs = [strtolower(igk_environment()->keyName())];
        if (($cnf = igk_environment()->extra_config) && ($cnf_file= igk_getv($cnf, "configFiles"))){
            $preload_configs = array_unique(array_merge($preload_configs, $cnf_file));
        }
        if ($fullpath==null){
            $fullpath = igk_io_syspath($file);
        }
        if ($preload_configs){
            $dir = dirname($fullpath); 
            foreach ($preload_configs as $value) {
                if (FileSystem::Exists($file = $dir."/configs.".$value.".php")){                     
                    $data = [];
                    IGK\System\Configuration\ConfigUtils::LoadData($file, $data);      
                    $extra = array_merge($extra, $data);
                };
            }
        }      
        $this->m_datas = new ConfigData($fullpath, $this, $this->m_configEntries, $extra);
        date_default_timezone_set( igk_getv($this->m_datas, 'date_time_zone', "Europe/Brussels")); 
        $db_name = $this->m_datas->db_name; 
    }
    /**
    * update cache
    */
    private function _updateCache(){
        $f=igk_io_syspath(IGK_CACHE_DATAFILE);
        $v_ctn = igk_notifyctrl();
        if($this->Data->cache_loaded_file){
            @unlink($f);
            $v_ctn->addMsg(__("Cache file stored"));
        }
        else{ 
            @unlink($f); 
            $v_ctn->addMsg(__("Unlink file: {0}", basename($f))); 
        }
    } 
    /**
    * 
    * @param mixed $obj
    * @param mixed $arg
    */

    public function addConfigSavedEvent($obj, $arg){
        igk_die(__METHOD__." Not Obselete");
    }
    /**
    * 
    * @param mixed $ctrl
    */

    public function checkConfigDataChanged($ctrl){
        $v=$ctrl->isChanged(IGKAppConfig::CHANGE_REG_KEY, $this->m_oldState);
        if($v){
            $this->_loadSystemConfig();
            return true;
        }
        return false;
    }
    /**
    * 
    */

    public function getConfigEntries(){
        return $this->m_configEntries;
    }
    /**
     * get data storage
    * @return \IGK\System\Configuration\ConfigData
    */

    public function getData(){
        return $this->m_datas;
    }
    /**
    * get singleton instance
    * @return self
    */

    public static function getInstance(){
        if(self::$sm_instance === null){
            self::$sm_instance=new IGKAppConfig();
        }
        return self::$sm_instance;
    }
    /**
    * 
    */

    public function onConfigSaved(){
        if($this->m_configSavedEvent){
            $this->m_configSavedEvent->Call($this, null);
        }
    }
    /**
    * 
    * @param mixed $obj
    * @param mixed $arg
    */

    public function removeConfigSavedEvent($obj, $arg){
        igk_die(__METHOD__." Not Obselete");
    }
    /**
    * save configuration 
    * @return bool save config result 
    */

    public function saveConfig($force=false){
        if($this->m_datas == null)
            return false;
        $this->m_datas->SortByKeys();
        if($this->m_datas->saveData($force)){ 
            $this->_updateCache();
            igk_sys_regchange(self::CHANGE_REG_KEY, $this->m_oldState);
            $this->onConfigSaved();
            return true;
        }
        return false;
    }
}