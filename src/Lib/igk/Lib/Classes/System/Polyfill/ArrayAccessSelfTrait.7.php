<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ArrayAccessSelfTrait.7.php
// @date: 20220803 13:48:55
// @desc: 


namespace IGK\System\Polyfill;

trait ArrayAccessSelfTrait{
    /**
     * 
     * @param mixed $n 
     * @param mixed $v 
     * @return void 
     */
    function offsetSet($n, $v){
        $this->_access_OffsetSet($n, $v);
    }
    /**
     * 
     * @param mixed $n 
     * @return mixed 
     */
    function offsetGet($n){
        return $this->_access_OffsetGet($n);
    }
    /**
     * 
     * @param mixed $n 
     * @return void 
     */
    function offsetUnset($n){
        $this->_access_OffsetUnset($n);
    }
    /**
     * 
     * @param mixed $n 
     * @return bool 
     */
    function offsetExists($n){
        return $this->_access_offsetExists($n);
    }
}