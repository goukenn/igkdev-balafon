<?php

namespace IGK\System\Polyfill;

trait MediaArrayAccessTrait
{
    ///<summary></summary>
    ///<param name="i"></param>
    /**
    * 
    * @param mixed $i
    */
    public function offsetExists($i):bool{
        return isset($this->_medias[$i]);
    }
    ///<summary></summary>
    ///<param name="key"></param>
    /**
    * 
    * @param mixed $key
    */
    public function offsetGet(mixed $key):mixed{
        return isset($this->_medias[$key]) ? $this->_medias[$key]: null;
    }
    ///<summary></summary>
    ///<param name="key"></param>
    ///<param name="value"></param>
    /**
    * 
    * @param mixed $key
    * @param mixed $value
    */
    public function offsetSet($key, $value):void{
        if($key === null)
            igk_die("key not valid");
        if((get_class($value) == IGKMedia::class) || is_subclass_of($value, IGKMedia::class)){
            $this->_medias[$key]=$value;
        }
    }
    ///<summary></summary>
    ///<param name="i"></param>
    /**
    * 
    * @param mixed $i
    */
    public function offsetUnset($i):void{
        unset($this->_medias[$i]);
    }
}