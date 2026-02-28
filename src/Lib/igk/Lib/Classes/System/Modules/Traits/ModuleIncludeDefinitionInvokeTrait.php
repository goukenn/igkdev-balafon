<?php
// @author: C.A.D. BONDJE DOUE
// @file: ModuleIncludeDefinitionInvokeTrait
// @date: 20260228 16:46:54
namespace IGK\System\Modules\Traits;

use IGK\System\Console\Logger;

/**
* 
* @package IGK
* @author C.A.D. BONDJE DOUE
*/
trait ModuleIncludeDefinitionInvokeTrait{
    protected function & getInvocationList(){
        throw new \Exception('must override implement this '.__FUNCTION__);
    }
    protected function invokeInclusion($name, $arguments){
        $list = & $this->getInvocationList() ?? [];
         if ($fc = igk_getv($list, $name)){
            try{
                if (!isset($list[$key = '::bindingList'])){
                    $list[$key] = [];
                }
                
                if (!isset($list[$key][$name])){
                    $fc = $fc->bindTo($this);
                    $list[$key][$name] = 1;
                    $list[$name] = $fc;                    
                }
                return call_user_func_array($fc, $arguments);
            }
            catch(\TypeError $ex){                
                throw $ex;
            }
        }else{
            throw new \TypeError('method not found');
        }
    }
}