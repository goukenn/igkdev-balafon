<?php
// @author: C.A.D. BONDJE DOUE
// @file: ActivatorPrivateInitProperty.php
// @date: 20241106 16:14:56
namespace IGK\Helper\Trait;
use Closure;
/**
* auto generate doc.
* @package IGK\Helper\Trait
* @author C.A.D. BONDJE DOUE
*/
trait ActivatorPrivateInitProperty{
    /**
     * create a private definition to handle 
     * @return Closure(mixed $def): void 
     */
    private static function _InitializePrivatePropertiesCallback(){
        return function ($def) {
            $inf = $this;
            foreach ($def as $k => $v) {
                if (property_exists($inf, $k)) {
                    $inf->{$k} = $v;
                }
            }
        };
    }
}