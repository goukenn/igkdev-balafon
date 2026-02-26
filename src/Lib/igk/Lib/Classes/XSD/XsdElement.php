<?php
// @author: C.A.D. BONDJE DOUE
// @filename: XsdElement.php
// @date: 20220803 13:48:54
// @desc: 
namespace IGK\XSD;
use ArrayAccess;
use IGK\System\Polyfill\ArrayAccessSelfTrait;

/**
* auto generate doc.
* @package IGK\XSD
*/
abstract class XsdElement implements ArrayAccess{
    use ArrayAccessSelfTrait;
    protected $m_node;

    /**
    * auto generate doc.
    */
    public function getNode(){
        return $this->m_node;
    }

    /**
    * auto generate doc.
    * @param mixed $offset
    */
    public function _array_offsetExists($offset)
    {
        return $this->m_node->offsetExists($offset);
    }

    /**
    * auto generate doc.
    * @param mixed $offset
    */
    public function _array_offsetGet($offset){
        return $this->m_node->offsetGet($offset);
    }

    /**
    * auto generate doc.
    * @param mixed $offset
    * @param mixed $value
    */
    public function _array_offsetSet($offset, $value){
        $this->m_node->offsetSet($offset, $value);
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $offset
    */
    public function _array_offsetUnset($offset){
        return $this->m_node->offsetUnset($offset);
    }
}