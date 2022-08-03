<?php
// @author: C.A.D. BONDJE DOUE
// @filename: XsdElement.php
// @date: 20220803 13:48:54
// @desc: 


namespace IGK\XSD;

use ArrayAccess;

abstract class XsdElement implements ArrayAccess{
    protected $m_node;

    public function getNode(){
        return $this->m_node;
    }

    public function offsetExists($offset):bool
    {
        return $this->m_node->offsetExists($offset);
    }

    public function offsetGet($offset): mixed
    {
        return $this->m_node->offsetGet($offset);
    }

    public function offsetSet($offset, $value):void
    {
        $this->m_node->offsetSet($offset, $value);
        return $this;
    }

    public function offsetUnset($offset) : void 
    {
        return $this->m_node->offsetUnset($offset);
    }
}