<?php
// @author: C.A.D. BONDJE DOUE
// @file: ArrayGetRefAccessSelfTrait.php
// @date: 20260404 09:40:55
namespace IGK\System\Polyfill;
/**
* auto generate doc.
* @package IGK\System\Polyfill
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\Polyfill
*/
trait ArrayGetRefAccessSelfTrait
{
    public abstract function offsetExists($n): bool;
    /**
    * auto generate doc.
    * @param mixed $n
    * @return void
    */
    public abstract function offsetUnset($n);
    /**
    * auto generate doc.
    * @param mixed $key
    * @return void
    */
    public function & OffsetGet($key)
    {
         return $this->_access_refoffset_get($key);
    }
    /**
    * auto generate doc.
    * @param mixed $n
    * @param mixed $v
    * @return void
    */
    public function OffsetSet($n, $v)
    {
        return $this->_access_offsetset($n, $v);
    }
    /**
    * auto generate doc.
    * @param mixed $key
    * @return void
    */
    protected abstract function & _access_refoffset_get($key);
}