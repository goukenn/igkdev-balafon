<?php
// @author: C.A.D. BONDJE DOUE
// @file: PropertyObjectTrait.php
// @date: 20241108 16:31:12
namespace IGK\System\Traits;
/**
* 
* @package IGK\System\Traits
* @author C.A.D. BONDJE DOUE
*/
trait PropertyObjectTrait{

    /**
    * .destructor
    * @param mixed $key
    */
    public function __get($key){
        if(method_exists($this, $fc = "get".ucfirst($key))){ 
            return call_user_func(array($this, $fc), array_slice(func_get_args(), 1));
        }
        return null;
    }

    /**
    * destructor
    * @param mixed $name
    * @param mixed $value
    */
    public function __set($name, $value){
        if(method_exists($this, $fc  = "set".ucfirst($name))){
            call_user_func(array($this, $fc), $value);
            return true;
        }
        return false;
    } 
}