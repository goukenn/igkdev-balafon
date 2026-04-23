<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ArrayAccessPropertyTrait.php
// @date: 20220803 13:48:55
// @desc:
namespace IGK\System\Polyfill;

/**
* Trait providing array access property functionality.
* @package IGK\System\Polyfill
*/
trait ArrayAccessPropertyTrait{
    use ArrayAccessSelfTrait;
    /**
     * Sets the value of a property by name.
     *
     * @param string $n The property name.
     * @param mixed  $v The value to assign.
     * @return void
     */
    function _access_OffsetSet($n, $v){
        $this->$n = $v;
    }
    /**
     * Gets the value of a property by name.
     *
     * @param string $n The property name.
     * @return mixed
     */
    function _access_OffsetGet($n){
        return $this->$n;
    }
    /**
     * Unsets a property by name (no-op).
     *
     * @param string $n The property name.
     * @return void
     */
    function _access_OffsetUnset($n){
    }
    /**
     * Checks whether a property exists on the object.
     *
     * @param string $n The property name.
     * @return bool
     */
    function _access_offsetExists($n){
        return property_exists($this, $n);
    }
}