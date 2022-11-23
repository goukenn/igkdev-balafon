<?php
// @author: C.A.D. BONDJE DOUE
// @file: InjectableTrait.php
// @date: 20221123 10:53:55
namespace IGK\System\Traits;

use IGK\Actions\Dispatcher;
use ReflectionMethod;

///<summary></summary>
/**
* 
* @package IGK\System\Traits
*/
trait InjectableTrait{
 /**
     * dispatch an invoke action method
     * @param string $function 
     * @param mixed $arguments 
     * @return mixed 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    protected function _dispatchAndInvoke(string $function, $arguments){
        $arguments = Dispatcher::GetInjectArgs(new ReflectionMethod($this, $function), $arguments);
        return $this->$function(...$arguments);
    }
}