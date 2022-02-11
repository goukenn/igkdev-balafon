<?php

namespace IGK\System\Polyfill;


trait ArrayAccessSelfTrait{
    function offsetSet(mixed $n, mixed $v):void{
        $this->_access_OffsetSet($n, $v);
    }
    function offsetGet(mixed $n):mixed{
        return $this->_access_OffsetGet($n);
    }
    function offsetUnset(mixed $n):void{
        $this->_access_OffsetUnset($n);
    }
    function offsetExists(mixed $n):bool{
        return  $this->_access_offsetExists($n); 
    }
}