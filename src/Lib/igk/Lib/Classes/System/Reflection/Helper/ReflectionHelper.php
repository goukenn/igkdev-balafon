<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReflectionHelper.php
// @date: 20231017 08:56:03
namespace IGK\System\Reflection\Helper;

use ReflectionProperty;

///<summary></summary>
/**
 * 
 * @package IGK\System\Reflection\Helper
 */
class ReflectionHelper
{
    /**
     * 
     * @param array<ReflectionParemeter> $parameters 
     * @param callable|null $filter 
     * @return void 
     */
    public static function GetParameterInfo(array $parameters, callable $callable = null)
    {
        // + | --------------------------------------------------------------------
        // + | get parameter dispatche info
        // + |    
        $v_params = [];
        $v_is_v8 = version_compare(PHP_VERSION, '8.0', '>=');
        
        foreach ($parameters as $info) {
            $n = $info->getName();
            $t = null;
            if ($info->hasType()) {
                $t = $info->getType()->getName();
            }
            $p = [];
            if ($info->isDefaultValueAvailable()) {
                $p['default'] = $info->getDefaultValue();
                if ($info->isDefaultValueConstant()) {
                    $p['ctn'] = $info->getDefaultValueConstantName();
                }
            }
            $p['is_optional'] = $info->isOptional();
            $p['is_ref'] = $info->isOptional();
            $p['is_variadic'] = $info->isVariadic();
            $p['is_promoted'] = $v_is_v8 ? $info->isPromoted() : false;
            $p['allow_null'] = $info->allowsNull();
            $v_params[$n] = (object)array_merge(['type' => $t], $p);
            // if callable - map data
            if ($callable) {
                $callable($n, $v_params[$n]);
            };
        }
        return $v_params;
    }

    public static function PropertyHasType(ReflectionProperty $prop){
        if (method_exists($prop, 'hasType')){
            return $prop->hasType();
        }
        return false;
    }
}
