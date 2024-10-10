<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKType.php
// @date: 20220803 13:48:54
// @desc: 

use IGK\Controllers\BaseController;
use IGK\System\IInjectable;
/**
 * manage type helper 
 * @package 
 */
class IGKType{

    public static function GetType($m){
        if (is_array($m)){
            return 'array';
        }
        if (is_object($m)){
            return get_class($m);
        }
        if (is_string($m)){
            return 'string';
        }
        if (is_int($m)){
            return 'int';
        }
        if (is_bool($m)){
            return 'boolean';
        }
        if (is_float($m)){
            return 'float';
        }
    }
    /**
     * 
     * @param ReflectionParameter $param 
     * @param string $base_type 
     * @return bool 
     */
    public static function ParameterIsTypeOf(ReflectionParameter $param, string $base_type):bool{
        $r = false;
        if ($param->hasType()){
            $g = $param->getType().'';
            if (IGKType::IsPrimaryType($g) && !IGKType::IsPrimaryType($base_type)){
                return false;
            }

            $r = ($g == $base_type) || is_subclass_of($g, $base_type); 
        }
        return $r;
    }
    /**
     * 
     * @param ReflectionType $t 
     * @return mixed 
     */
    public static function GetName(ReflectionType $t){
        //+ work arround to avoid getName not implement in php 7.+ 8.0
        if (method_exists($t, $fc = "getName")){
            return $t->$fc();
        }                
    }
    /**
     * check if is primary type
     * @param string $type 
     * @return bool 
     */
    public static function IsPrimaryType(string $type){
        return in_array($type, explode('|', "int|float|bool|double|void|array|string|callable"));
    }
    /**
     * check if type is injectable 
     * @param string $type 
     * @return bool 
     */
    public static function IsInjectable(string $type):bool{
        return is_subclass_of($type, IInjectable::class) || 
              ($type==BaseController::class) || is_subclass_of($type, BaseController::class) ;
    }
    /**
     * get if methodName is a magic function
     * @param string $methodName 
     * @return bool 
     */
    public static function IsMagicMethod(string $methodName):bool{
        return in_array($methodName, explode("|", 
            "__construct|__get|__call|__classStatic|__isset|__wakeup|__sleep|__toString"));
    }
}