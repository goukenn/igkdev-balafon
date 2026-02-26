<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ArrayAccessSelfTrait.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Polyfill;

/**
* Trait providing array access self functionality.
* @package IGK\System\Polyfill
*/
trait ArrayAccessSelfTrait{
    /**
     * 
     * @param mixed $n 
     * @param mixed $v 
     * @return void 
     */
    function offsetSet(mixed $n, mixed $v):void{
        $this->_access_OffsetSet($n, $v);
    }
    /**
     * 
     * @param mixed $n 
     * @return mixed 
     */
    function offsetGet(mixed $n):mixed{
        $g =  $this->_access_OffsetGet($n);
        return $g;
    }

    /**
    * Offset unset.
    * @param mixed $n
    * @return void
    */
    function offsetUnset(mixed $n):void{
        $this->_access_OffsetUnset($n);
    }

    /**
    * Offset exists.
    * @param mixed $n
    * @return bool
    */
    function offsetExists(mixed $n):bool{
        return  $this->_access_offsetExists($n); 
    }
}