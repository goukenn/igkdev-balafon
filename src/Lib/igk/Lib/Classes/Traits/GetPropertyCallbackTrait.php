<?php
// @author: C.A.D. BONDJE DOUE
// @file: GetPropertyCallbackTrait.php
// @date: 20250129 17:58:00
namespace IGK\Traits;

/**
* 
* @package IGK\Traits
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\Traits
*/
trait GetPropertyCallbackTrait{
    /**
    * auto generate doc.
    * @param mixed $key
    * @return mixed
    */
    public function __get($key){
        if(method_exists($this, $fc = "get".$key)){
            return call_user_func([$this, $fc], null);
        }
    }
}