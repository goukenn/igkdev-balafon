<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ItemsArrayAccessTrait.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Polyfill;

/**
* Trait providing items array access functionality.
* @package IGK\System\Polyfill
*/
trait ItemsArrayAccessTrait{
    /**
    * Collection of items.
    * @var mixed
    */
    protected $m_items;
    /**
    * auto generate doc.
    * @param mixed $key
    */
    public function offsetExists($key){
        return isset($this->m_items[$key]);
    }
    /**
    * auto generate doc.
    * @param mixed $key
    */
    public function offsetGet($key){
        return $this->m_items[$key];
    }
    /**
    * auto generate doc.
    * @param mixed $value
    */
    public function offsetSet($key, $value){
        $this->m_items[$key]=$value;
    }
    /**
    * auto generate doc.
    * @param mixed $key
    */
    public function offsetUnset($key){
        unset($this->m_items[$key]);
    }
}