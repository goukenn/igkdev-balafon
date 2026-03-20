<?php
// @author: C.A.D. BONDJE DOUE
// @file: ModuleIncludeDefinitionInvokeTrait
// @date: 20260228 16:46:54
namespace IGK\System\Modules\Traits;

use Closure;
use IGK\System\Console\Logger;
use IGK\System\Excpetions\ApplicationModuleControllerException;
use ReflectionMethod;

/**
 * 
 * @package IGK
 * @author C.A.D. BONDJE DOUE
 */

/**
 * auto generate doc.
 * @package IGK\System\Modules\Traits
 */
trait ModuleIncludeDefinitionInvokeTrait
{
    /**
     * retrieve invocation method 
     * @return never 
     * @throws mixed 
     */
    protected function &getInvocationList()
    {
        throw new \Exception('must override implement this ' . __FUNCTION__);
    }
    /**
     * invoke inclusion 
     * @param mixed $name 
     * @param mixed $arguments 
     * @return mixed 
     * @throws mixed 
     */
    protected function invokeInclusion($name, $arguments)
    {
        $key = '::bindingList';
        $list = &$this->getInvocationList() ?? [];
        if (!isset($list[$key])) {
            $list[$key] = [];
        }
        if ($fc = igk_getv($list[$key], $name) ?? igk_getv($list, $name)) {
            if (is_array($fc)) {
                $m = Closure::fromCallable($fc);
                if (is_string($cl = $fc[0]) && class_exists($cl)) {
                    $tc = new ReflectionMethod($cl, $fc[1]);
                    if ($tc->isStatic()) {
                        $list[$key][$name] = $m;
                    }
                }
                $fc = $m;
            }
            try {
                if (!isset($list[$key][$name])) {
                    $fc = $fc->bindTo($this);
                    $list[$key][$name] = 1;
                    $list[$name] = $fc;
                }
                return call_user_func_array($fc, $arguments);
            } catch (\TypeError $ex) {
                throw new ApplicationModuleControllerException($this, $ex->getMessage(), 500, $ex);
            } catch (\Exception $ex) {
                throw new ApplicationModuleControllerException($this, $ex->getMessage(), 500, $ex);
            }
        } else {
            throw new \TypeError('method not found');
        }
    }
}
