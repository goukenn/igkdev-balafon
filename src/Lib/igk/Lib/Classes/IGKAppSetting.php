<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKAppSetting.php
// @date: 20220803 13:48:54
// @desc: 
///<summary>

/**
* auto generate doc.
* @package IGK
* @property \IGKAppInfoStorage $appInfo application storage info
*/
class IGKAppSetting{

    /**
    * Property: info.
    * @var mixed
    */
    private $info;

    /**
    * Returns Info.
    */
    public function getInfo(){
        return $this->info;
    }
    /**
     * create with info
     * @param object $info 
     * @return void 
     */

    public function __construct(object $info){
        if (!is_object($info)) die("info not valid");
        $this->info = $info;
    }

    /**
    * Called after unserialize().
    */
    public function __wakeup()
    {
        igk_wln_e("wake up appsetting");
    }

    /**
    * .destructor
    * @param mixed $n
    */
    public function __get($n){
        if (method_exists($this, $fc = "get".ucfirst($n))){
            return call_user_func_array([$this, $fc], []);
        }
        if (isset($this->info->$n)){
            return $this->info->$n;
        }
        return null;
    }

    /**
    * destructor
    * @param mixed $n
    * @param mixed $v
    */
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

    /**
    * Used by var_dump() to customize debug output.
    */
    public function __debugInfo()
    {
        return [];
    }
}