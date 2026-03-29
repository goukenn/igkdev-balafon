<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ItemsArrayAccessTrait.8.php
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
    public function offsetExists($key):bool{
        return isset($this->m_items[$key]);
    }
    /**
    * auto generate doc.
    * @param mixed $key
    */
    public function offsetGet(mixed $key):mixed{
        return $this->m_items[$key];
    }
    /**
    * auto generate doc.
    * @param mixed $value
    */
    public function offsetSet($key, $value):void{
        $this->m_items[$key]=$value;
    }
    /**
    * auto generate doc.
    * @param mixed $key
    */
    public function offsetUnset($key):void{
        unset($this->m_items[$key]);
    }
}