<?php

namespace IGK\System\Configuration;

use IGK\Resources\R;
use IGK\System\IO\FileWriter;
use IGKCSVDataAdapter;
use function igk_resources_gets as __;

///<summary>represent system config data - </summary>
/**
* represent system config data -
*/
final class ConfigData {
    private $m_configCtrl;
    private $m_configEntries;
    private $m_confile;
    ///full path to
    ///conffile : configuration file
    ///configctrl : hosted controller
    ///entries: default entry
    /**
    */
    public function __construct($conffile, $configCtrl, $entries){
        $this->m_confile=$conffile;
        $this->m_configCtrl=$configCtrl;
        $this->m_configEntries=$entries;
    }
    ///<summary></summary>
    ///<param name="key"></param>
    /**
    * 
    * @param mixed $key
    */
    public function __get($key){
        return igk_getv($this->m_configEntries, $key);
    }
    ///<summary></summary>
    ///<param name="key"></param>
    /**
    * 
    * @param mixed $key
    */
    public function __isset($key){
        return isset($this->m_configEntries[$key]);
    }
    ///<summary></summary>
    ///<param name="key"></param>
    ///<param name="value"></param>
    /**
    * 
    * @param mixed $key
    * @param mixed $value
    */
    public function __set($key, $value){ 
        if(isset($this->m_configEntries[$key])){
            if($value === null){
                unset($this->m_configEntries[$key]);
            }
            else{
                $this->m_configEntries[$key]=$value;
            }
        }
        else {
            if (($value !== null) && !(is_string($value)&& empty($value))){
                $this->m_configEntries[$key]=$value;
            } 
        } 
    }
    ///<summary>display value</summary>
    /**
    * display value
    */
    public function __toString(){
        return "IGKConfigurationData [Count: ".count($this->m_configEntries)."]";
    }
    ///<summary></summary>
    ///<param name="name"></param>
    ///<param name="default" default="null"></param>
    /**
    * 
    * @param mixed $name
    * @param mixed $default the default value is null
    */
    public function getConfig($name, $default=null){
        return igk_getv($this->m_configEntries, $name, $default);
    }
    public function get($xpath, $default= null){
        return igk_conf_get($this->m_configEntries, $xpath, $default);
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getEntries(){
        return $this->m_configEntries;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getEntriesKeys(){
        return array_keys($this->m_configEntries);
    }
    ///<summary></summary>
    /**
    * 
    */
    public function saveData($force=false){
        if(!$force && defined("IGK_FRAMEWORK_ATOMIC")){
            return false;
        } 
        $file=$this->m_confile; 
        $m = igk_map_array_to_str($this->m_configEntries);  
        $out = igk_cache_array_content( $m, $file);           
        ($r = igk_io_w2file($file, $out, true)) && FileWriter::Invalidate($file);
        return $r;
    }
    public function set($name, $entries){
        if (is_array($entries)){
            igk_trace();
            igk_exit();
        }
        $k = key($entries);
        $v = array_unshift($entries);
        
        while( count($entries)>0){           
            $k = key($entries);
            $v = array_shift($entries);
            $key = implode(".", [$name, $k]);           
            if (is_array($v)){
                die("not allowed");
            }else{
                $this->m_configEntries[$key] = $v;
            }  
        }
        $this->saveData();
    }
    ///<summary></summary>
    ///<param name="name"></param>
    ///<param name="value"></param>
    /**
    * 
    * @param mixed $name
    * @param mixed $value
    */
    public function setConfig($name, $value){
        if($name)
            $this->m_configEntries[$name]=$value;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function SortByKeys(){
        $keys=array_keys($this->m_configEntries);
        sort($keys);
        $t=array();
        foreach($keys as $k){
            $t[$k]=$this->m_configEntries[$k];
        }
        $this->m_configEntries=$t;
    }
    /**
     * get language settings
     * @param mixed $key 
     * @return mixed 
     */
    public function getLangSetting($key){
        $ckey = "@sysconfig:".$key;
        if (R::Contains($ckey)){
            return  __($ckey) ;  
        } 
        return $this->get($key); 
    }

    public function menu_default_page(){
        return $this->get("menu_default_page", "default");
    }
    public function reload(){ 
        $this->m_configEntries = include($this->m_confile) ?? []; 
    }
}