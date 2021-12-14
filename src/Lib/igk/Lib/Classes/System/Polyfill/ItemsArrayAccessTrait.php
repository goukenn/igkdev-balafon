<?php
namespace IGK\System\Polyfill;

trait ItemsArrayAccessTrait{
    protected $m_items;
     ///<summary></summary>
    ///<param name="key"></param>
    /**
    * 
    * @param mixed $key
    */
    public function offsetExists($key){
        return isset($this->m_items[$key]);
    }
    ///<summary></summary>
    ///<param name="key"></param>
    /**
    * 
    * @param mixed $key
    */
    public function offsetGet($key){
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
    public function offsetSet($key, $value){
        $this->m_items[$key]=$value;
    }
    ///<summary></summary>
    ///<param name="key"></param>
    /**
    * 
    * @param mixed $key
    */
    public function offsetUnset($key){
        unset($this->m_items[$key]);
    }
}
 