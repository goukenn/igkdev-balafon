<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ItemsArrayAccessTrait.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Polyfill;

/**
* auto generate doc.
* @package IGK\System\Polyfill
*/
trait ItemsArrayAccessTrait{
    protected $m_items;
     /**
    * 
    * @param mixed $key
    */
    public function offsetExists($key){
        return isset($this->m_items[$key]);
    }
    /**
    * 
    * @param mixed $key
    */
    public function offsetGet($key){
        return $this->m_items[$key];
    }
    /**
    * 
    * @param mixed $key
    * @param mixed $value
    */
    public function offsetSet($key, $value){
        $this->m_items[$key]=$value;
    }
    /**
    * 
    * @param mixed $key
    */
    public function offsetUnset($key){
        unset($this->m_items[$key]);
    }
}