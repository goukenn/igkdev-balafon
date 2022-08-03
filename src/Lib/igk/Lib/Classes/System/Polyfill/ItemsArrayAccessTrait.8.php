<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ItemsArrayAccessTrait.8.php
// @date: 20220803 13:48:55
// @desc: 

namespace IGK\System\Polyfill;

trait ItemsArrayAccessTrait{
    protected $m_items;
     ///<summary></summary>
    ///<param name="key"></param>
    /**
    * 
    * @param mixed $key
    */
    public function offsetExists($key):bool{
        return isset($this->m_items[$key]);
    }
    ///<summary></summary>
    ///<param name="key"></param>
    /**
    * 
    * @param mixed $key
    */
    public function offsetGet(mixed $key):mixed{
        return $this->m_items[$key];
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
        $this->m_items[$key]=$value;
    }
    ///<summary></summary>
    ///<param name="key"></param>
    /**
    * 
    * @param mixed $key
    */
    public function offsetUnset($key):void{
        unset($this->m_items[$key]);
    }
}