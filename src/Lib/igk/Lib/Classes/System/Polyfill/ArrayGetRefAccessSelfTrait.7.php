<?php
// @author: C.A.D. BONDJE DOUE
// @file: ArrayGetRefAccessSelfTrait.php
// @date: 20260404 09:40:55
namespace IGK\System\Polyfill;

/**
* 
* @package IGK\System\Polyfill
* @author C.A.D. BONDJE DOUE
*/
trait ArrayGetRefAccessSelfTrait
{
    public abstract function offsetExists($n): bool;
    public abstract function offsetUnset($n);
    public function & OffsetGet($key)
    {
         return $this->_access_refoffset_get($key);
    }
    public function OffsetSet($n, $v)
    {
        return $this->_access_offsetset($n, $v);
    }
    protected abstract function & _access_refoffset_get($key);
}