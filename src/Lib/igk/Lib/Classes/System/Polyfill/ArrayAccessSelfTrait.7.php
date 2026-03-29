<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ArrayAccessSelfTrait.7.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Polyfill;
/**
* Trait providing array access self functionality.
* @package IGK\System\Polyfill
*/
trait ArrayAccessSelfTrait{
    /**
    * auto generate doc.
    * @param mixed $v
    * @return void
    */
    function offsetSet($n, $v){
        $this->_access_OffsetSet($n, $v);
    }
    /**
    * auto generate doc.
    * @param mixed $n
    * @return mixed
    */
    function offsetGet($n){
        return $this->_access_OffsetGet($n);
    }
    /**
    * auto generate doc.
    * @param mixed $n
    * @return void
    */
    function offsetUnset($n){
        $this->_access_OffsetUnset($n);
    }
    /**
    * auto generate doc.
    * @param mixed $n
    * @return bool
    */
    function offsetExists($n){
        return $this->_access_offsetExists($n);
    }
}