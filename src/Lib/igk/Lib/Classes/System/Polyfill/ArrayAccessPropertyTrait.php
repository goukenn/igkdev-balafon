<?php

namespace IGK\System\Polyfill;

trait ArrayAccessPropertyTrait{
    use ArrayAccessSelfTrait;
    function _access_OffsetSet($n, $v){
        $this->$n = $v;
    }
    function _access_OffsetGet($n){
        return $this->$n;
    }
    function _access_OffsetUnset($n){
        // do nothing
    }
    function _access_offsetExists($n){
        return property_exists($this, $n);
    }
}