<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ArrayAccessPropertyTrait.php
// @date: 20220803 13:48:55
// @desc: 


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