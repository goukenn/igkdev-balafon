<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ArrayAccessSelfTrait.7.php
// @date: 20220803 13:48:55
// @desc: 


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