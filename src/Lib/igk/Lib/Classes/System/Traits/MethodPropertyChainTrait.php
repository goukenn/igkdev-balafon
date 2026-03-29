<?php
// @author: C.A.D. BONDJE DOUE
// @filename: MethodPropertyChainTrait.php
// @date: 20220803 13:50:44
// @desc: 
namespace IGK\System\Traits;
/**
* auto generate doc.
*/
trait MethodPropertyChainTrait{
    /**
    * Triggered when calling an inaccessible or undefined method on an object.
    * @param mixed $n
    * @param mixed $args
    */
    public function __call($n, $args){
        method_exists($this, "isAllowed") || igk_die("isAllowed method is missing in ".static::class); 
        if ($this->isAllowed($n, $args)){
            if (count($args)==1)
                $this->$n = $args[0];
            else {
                $this->$n = $args;
            }
        }
        return $this;
    }
    // use of this trait require a isAllowed method in order to work properly
    /**
    * .destructor
    * @param mixed $n
    */
    public function __get($n){
        method_exists($this, "isAllowed") || igk_die("isAllowed method is missing in ".static::class); 
        if ($this->isAllowed($n, null)){
            return null;
        } else {
            igk_die("property not allowed");
        }
    }
}