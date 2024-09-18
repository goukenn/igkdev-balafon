<?php
// @author: C.A.D. BONDJE DOUE
// @file: InvokeMethodActionTrait.php
// @date: 20221123 10:26:39
namespace IGK\Actions\Traits;

use IGK\System\Traits\InjectableTrait;

///<summary></summary>
/**
* invoke method trait
* @package IGK\Actions\Traits
*/
trait InvokeMethodActionTrait{
    use InjectableTrait;
 /**
     * check if method exists helper 
     * @param string $pattern 
     * @param null|string $name 
     * @return null|string 
     */
    protected function _method_exists(string $pattern, ?string $name=null):?string{
        $fc = sprintf($pattern, $name ?? '');
        if (method_exists($this, $fc)){
            return $fc;
        }
        return null; 
    }
    /**
     * 
     * @param string $function 
     * @param array $arguments 
     * @return mixed 
     */
    protected function _invoke_method(string $function, array $arguments){
        return $this->_dispatchAndInvoke($function, $arguments); 
    }
}