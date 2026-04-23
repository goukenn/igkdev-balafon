<?php
// @author: C.A.D. BONDJE DOUE
// @filename: XsdElement.php
// @date: 20220803 13:48:54
// @desc: 
namespace IGK\XSD;
use ArrayAccess;
use IGK\System\Polyfill\ArrayAccessSelfTrait;

/**
* Xsd element.
* @package IGK\XSD
*/
abstract class XsdElement implements ArrayAccess{
    use ArrayAccessSelfTrait;
    /**
    * Property: node.
    * @var mixed
    */
    protected $m_node;
    /**
    * Returns Node.
    */
    public function getNode(){
        return $this->m_node;
    }
    /**
    * Array offset exists.
    * @param mixed $offset
    */
    public function _array_offsetExists($offset)
    {
        return $this->m_node->offsetExists($offset);
    }
    /**
    * Array offset get.
    * @param mixed $offset
    */
    public function _array_offsetGet($offset){
        return $this->m_node->offsetGet($offset);
    }
    /**
    * Array offset set.
    * @param mixed $offset
    * @param mixed $value
    */
    public function _array_offsetSet($offset, $value){
        $this->m_node->offsetSet($offset, $value);
        return $this;
    }
    /**
    * Array offset unset.
    * @param mixed $offset
    */
    public function _array_offsetUnset($offset){
        return $this->m_node->offsetUnset($offset);
    }
}