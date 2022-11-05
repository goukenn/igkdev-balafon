<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ArrayAccessSelfTrait.php
// @date: 20220803 13:48:55
// @desc: 


namespace IGK\System\Polyfill;


trait ArrayAccessSelfTrait{
    function offsetSet(mixed $n, mixed $v):void{
        $this->_access_OffsetSet($n, $v);
    }
    function offsetGet(mixed $n):mixed{
        $g =  $this->_access_OffsetGet($n);

        return $g;
    }
    function offsetUnset(mixed $n):void{
        $this->_access_OffsetUnset($n);
    }
    function offsetExists(mixed $n):bool{
        return  $this->_access_offsetExists($n); 
    }
}