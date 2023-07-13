<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKAppConfig.php
// @date: 20220803 13:48:54
// @desc: 


///<summary>Represente class: IGKAppConfig</summary>

use IGK\System\Configuration\ConfigData;
use function igk_resources_gets as __; 
/**
* Represente IGKAppConfig class
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
* @property bool $website_prefix
* @property bool $website_title
*/
final class IGKAppConfig extends IGKObject {
    const CHANGE_REG_KEY="IGKConfigDataChanged";
    private $m_configEntries;
    private $m_configSavedEvent;
    private $m_datas;
    private $m_oldState;
    /** @var IGKAppConfig */
    private static $sm_instance;
    ///<summary></summary>
    /**
    * 
    */
    private function __construct(){
        $this->_loadSystemConfig();
    }
   
    ///<summary>load configuration files </summary>
    /**
    * load configuration files
    */
    private function _loadSystemConfig(){
        $file=IGK_CONF_DATA;
        $this->m_configEntries=array();
        $extra = [];
        $fullpath = null;
        $b = igk_io_basenamewithoutext($file);
        $dir = dirname(igk_io_syspath($file));
        foreach(["", ".".igk_environment()->name()] as $f){
            $n = $dir."/".$b.$f.".php"; 
            if (file_exists($n = $dir."/".$b.$f.".php")){        
                $fullpath=$n;
                IGK\System\Configuration\ConfigUtils::LoadData($fullpath, $this->m_configEntries);      
            } 
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
                if (file_exists($file = $dir."/configs.".$value.".php")){                     
                    $data = [];
                    IGK\System\Configuration\ConfigUtils::LoadData($file, $data);      
                    $extra = array_merge($extra, $data);
                    //igk_wln_e("prelead....", $preload_configs, $this->m_configEntries + $extra);
                };
            }
        }      
        $this->m_datas = new ConfigData($fullpath, $this, $this->m_configEntries, $extra);
        // gk_wln_e("finish", $this->m_datas)   ;
        date_default_timezone_set( igk_getv($this->m_datas, 'date_time_zone', "Europe/Brussels"));         
      
    }
    ///<summary></summary>
    /**
    * 
    */
    private function _updateCache(){
        $f=igk_io_syspath(IGK_CACHE_DATAFILE);
        if($this->Data->cache_loaded_file){
            if(file_exists($f))
                @unlink($f);
            igk_notifyctrl()->addMsg(__("Cache file stored"));
        }
        else{
            if(file_exists($f)){
                $c=@unlink($f);
                $t=@unlink(self::_LibCacheFile());
                igk_notifyctrl()->addMsg(__("Unlink file: {0}", basename($f)));
            }
        }
    }
    private static function _LibCacheFile(){
        die(__METHOD__);
    }
    ///<summary></summary>
    ///<param name="obj"></param>
    ///<param name="arg"></param>
    /**
    * 
    * @param mixed $obj
    * @param mixed $arg
    */
    public function addConfigSavedEvent($obj, $arg){
        igk_die(__METHOD__." Not Obselete");
    }
    ///<summary></summary>
    ///<param name="ctrl"></param>
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
    ///<summary></summary>
    /**
    * 
    */
    public function getConfigEntries(){
        return $this->m_configEntries;
    }
    ///<summary></summary>
    /**
     * get data storage
    * @return \IGK\System\Configuration\ConfigData
    */
    public function getData(){
        return $this->m_datas;
    }
    ///<summary></summary>
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
    ///<summary></summary>
    /**
    * 
    */
    public function onConfigSaved(){
        if($this->m_configSavedEvent){
            $this->m_configSavedEvent->Call($this, null);
        }
    }
    ///<summary></summary>
    ///<param name="obj"></param>
    ///<param name="arg"></param>
    /**
    * 
    * @param mixed $obj
    * @param mixed $arg
    */
    public function removeConfigSavedEvent($obj, $arg){
        igk_die(__METHOD__." Not Obselete");
    }
    ///<summary></summary>
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