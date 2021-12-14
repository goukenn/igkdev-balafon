<?php

///<summary>Represente class: IGKAppConfig</summary>

use IGK\System\Configuration\ConfigData;

/**
* Represente IGKAppConfig class
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
        // require_once(IGK_LIB_DIR."/Lib/Classes/System/Configuration/ConfigUtils.php");
        // require_once(IGK_LIB_DIR."/Lib/Classes/IGKCSVDataAdapter.php");
        $file=IGK_CONF_DATA;
        $this->m_configEntries=array();
        $fullpath=igk_io_syspath($file);
        // $s = igk_sys_request_time();
        // igk_debug_wln("Before loading configuration util:::::::".$s . " ? ".file_exists($fullpath));
        IGK\System\Configuration\ConfigUtils::LoadData($fullpath, $this->m_configEntries);      
        // $m = igk_sys_request_time();  
        // igk_debug_wln("After loading configuration util:::::::::".$m);// = igk_sys_request_time()));
        // igk_wln_e("duration ::: ".($m - $s));
        $this->m_datas = new ConfigData($fullpath, $this, $this->m_configEntries);
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
    * 
    */
    public function getData(){
        return $this->m_datas;
    }
    ///<summary></summary>
    /**
    * 
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