<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKType.php
// @date: 20220803 13:48:54
// @desc: 
use IGK\System\IInjectable;
/**
 * manage type helper 
 * @package 
 */
class IGKType{
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
        return is_subclass_of($type, IInjectable::class);
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