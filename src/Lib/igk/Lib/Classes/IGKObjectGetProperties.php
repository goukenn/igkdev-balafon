<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKObjectGetProperties.php
// @date: 20220803 13:48:54
// @desc: 



///<summary>Abstract magic to get/set properties</summary>
/**
* Abstract magic to get/set propertie
*/
abstract class IGKObjectGetProperties{
    ///<summary></summary>
    ///<param name="key"></param>
    /**
    * 
    * @param mixed $key
    * @return mixed 
    */
    public function __get($key){
        if(method_exists($this, "get".$key)){
            return call_user_func(array($this, "get".$key), null);
        }
    }
    ///<summary></summary>
    ///<param name="key"></param>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $key
    * @param mixed $v
    */
    public function __set($key, $v){
        $nk="set".$key;
        if(method_exists($this, $nk)){
            return call_user_func_array(array($this, $nk), array($v));
        }
    }
}