<?php
// @author: C.A.D. BONDJE DOUE
// @file: GetPropertyCallbackTrait.php
// @date: 20250129 17:58:00
namespace IGK\Traits;


///<summary></summary>
/**
* 
* @package IGK\Traits
* @author C.A.D. BONDJE DOUE
*/
trait GetPropertyCallbackTrait{
    ///<summary></summary>
    ///<param name="key"></param>
    /**
    * 
    * @param mixed $key
    * @return mixed 
    */
    public function __get($key){
        if(method_exists($this, $fc = "get".$key)){
            return call_user_func([$this, $fc], null);
        }
    }
}