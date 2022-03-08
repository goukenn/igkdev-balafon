<?php

///<summary>
/**
 * 
 * @package IGK
 * @property \IGKAppInfoStorage $appInfo application storage info
 */
class IGKAppSetting{
    private $info;
    public function getInfo(){
        return $this->info;
    }
    public function __construct($info){
        if (!is_object($info)) die("info not valid");
        $this->info = $info;
    }
    public function __get($n){
        if (method_exists($this, $fc = "get".ucfirst($n))){
            return call_user_func_array([$this, $fc], []);
        }
        if (isset($this->info->$n)){
            return $this->info->$n;
        }
        return null;
    }
    public function __set($n, $v){
        if ($v === null){
            unset($this->info->$n);
        } else {
            $this->info->$n = $v;
        }
        return $this;
    }
    /**
     * return application storage info
     * @return IGKAppInfoStorage 
     */
    public function getAppInfo(){
        static $storage;
        if (($storage===null) || ($storage!== $this->info->appInfo)){
            $storage = new IGKAppInfoStorage($this->info->appInfo);
        }
        return $storage;
    }
    public function __debugInfo()
    {
        return [];
    }
}