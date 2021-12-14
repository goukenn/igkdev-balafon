<?php

namespace IGK\System\Polyfill;

trait ArrayAccessSelfTrait{
    function offsetSet($n, $v){
        $this->_access_OffsetSet($n, $v);
    }
    function offsetGet($n){
        return $this->_access_OffsetGet($n);
    }
    function offsetUnset($n){
        $this->_access_OffsetUnset($n);
    }
    function offsetExists($n){
        return $this->_access_offsetExists($n);
    }
}