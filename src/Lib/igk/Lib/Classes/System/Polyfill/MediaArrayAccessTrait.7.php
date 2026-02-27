<?php
// @author: C.A.D. BONDJE DOUE
// @filename: MediaArrayAccessTrait.7.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Polyfill;

/**
* Trait providing media array access functionality.
* @package IGK\System\Polyfill
*/
trait MediaArrayAccessTrait
{

    /**
    * auto generate doc.
    * @param mixed $i
    */
    public function offsetExists($i):bool{
        return isset($this->_medias[$i]);
    }

    /**
    * auto generate doc.
    * @param mixed $key
    */
    public function offsetGet($key){
        return isset($this->_medias[$key]) ? $this->_medias[$key]: null;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */
    public function offsetSet($key, $value){
        if($key === null)
            igk_die("key not valid");
        if((get_class($value) == IGKMedia::class) || is_subclass_of($value, IGKMedia::class)){
            $this->_medias[$key]=$value;
        }
    }

    /**
    * auto generate doc.
    * @param mixed $i
    */
    public function offsetUnset($i):void{
        unset($this->_medias[$i]);
    }
}