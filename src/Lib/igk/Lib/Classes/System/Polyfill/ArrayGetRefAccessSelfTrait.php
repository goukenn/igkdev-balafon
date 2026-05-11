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
    public function offsetExists(mixed $n): bool{
        return $this->_access_offset_exists($n);
    }
    /**
    * auto generate doc.
    * @param mixed $n
    * @return void
    */
    public function offsetUnset(mixed $n): void{
        $this->_access_offset_unset($n);
    }
    /**
    * auto generate doc.
    * @param mixed $key
    * @return mixed
    */
    public function & OffsetGet(mixed $key): mixed
    {
        $p = & $this->_access_refoffset_get($key); 
        return $p;
    }
    /**
    * auto generate doc.
    * @param mixed $n
    * @param mixed $v
    * @return void
    */
    public function OffsetSet(mixed $n, mixed $v): void
    {
        if (method_exists($this, $fc = 'set'.ucfirst($n))){
            call_user_func_array([$this, $fc], [$v]);
            return;
        }
        $this->_access_offsetset($n, $v);
    }
    /**
    * auto generate doc.
    * @param mixed $key
    * @return void
    */
    protected abstract function & _access_refoffset_get($key);
}