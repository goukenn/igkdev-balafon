<?php
// @author: C.A.D. BONDJE DOUE
// @file: SetPropertyCallbackTrait.php
// @date: 20250129 17:59:06
namespace IGK\Traits;
/**
 * 
 * @package IGK\Traits
 * @author C.A.D. BONDJE DOUE
 */
trait SetPropertyCallbackTrait
{
    /**
     * 
     * @param mixed $key
     * @param mixed $v
     */
    public function __set($key, $v)
    {
        $nk = "set" . $key;
        if (method_exists($this, $nk)) {
            return call_user_func_array(array($this, $nk), array($v));
        }
    }
}