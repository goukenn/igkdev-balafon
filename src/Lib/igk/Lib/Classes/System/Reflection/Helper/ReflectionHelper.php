<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReflectionHelper.php
// @date: 20231017 08:56:03
namespace IGK\System\Reflection\Helper;
use ReflectionProperty;

/**
 * 
 * @package IGK\System\Reflection\Helper
 */
/**
* auto generate doc.
* @package IGK\System\Reflection\Helper
*/
class ReflectionHelper
{
    /**
     * retrieve public object vars. accessible outside the ReflectionHelper class 
     * @param mixed $obj 
     * @return array 
     */
    public static function GetObjectVars($obj): array{
        return get_object_vars($obj);
    }
    /**
    * auto generate doc.
    * @param callable|null $filter
    * @return void
    */
    public static function GetParameterInfo(array $parameters, ?callable $callable = null)
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
            if ($callable) {
                $callable($n, $v_params[$n]);
            };
        }
        return $v_params;
    }
    /**
    * Property has type.
    * @param ReflectionProperty $prop
    */
    public static function PropertyHasType(ReflectionProperty $prop){
        if (method_exists($prop, 'hasType')){
            return $prop->hasType();
        }
        return false;
    }
    /**
    * auto generate doc.
    * @param mixed $i
    * @return array
    */
    public static function DebugOnlyPublicMember($i):array{
        $r = [];
        $tab = (array)$i;
        foreach($tab as $k=>$v){
            if (false === strpos($k, "\0")){
                $r[$k] = $v;
            }
        }
        return $r;
    }
}