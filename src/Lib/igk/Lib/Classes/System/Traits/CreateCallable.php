<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CreateCallable.php
// @date: 20220803 13:48:55
// @desc:
namespace IGK\System\Traits;
use Closure;
trait CreateCallable{
    /**
     * Creates a callable closure bound to the given object.
     *
     * @param object $t      The object to bind the closure to.
     * @param string $func   The method name to invoke on the bound object.
     * @param mixed  ...$params Additional parameters to pass to the method.
     * @return Closure
     */
    public static function CreateCallable($t, $func, ...$params){
        return Closure::fromCallable(function()use($func, $params){
               $args = array_merge(func_get_args(), $params);
               return $this->$func(...$args);
        })->bindTo($t);
    }
}
