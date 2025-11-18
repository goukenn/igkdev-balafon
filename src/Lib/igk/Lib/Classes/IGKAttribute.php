<?php
// @file: IGKAttribute.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
class IGKAttribute extends IGKObject{
    static $classAttributes=array();
    public function __construct(){    }
    public static function GetAttributes($classOrObject){
        $n=null;
        if(is_string($classOrObject)){
            $n=$classOrObject;
        }
        else
            $n=get_class($classOrObject);
        return igk_getv(self::$classAttributes, $n);
    }
    public static function Register($classname, $attribute, $allowmultiple=true, $inherits=false){
        $n=get_class($attribute);
        if(class_exists($classname)){
            if(igk_reflection_class_extends($n, __CLASS__)){
                if(($tab=igk_getv(self::$classAttributes, $classname)) == null){
                    $tab=array();
                }
                $tab[]=$attribute;
                self::$classAttributes[$classname]=$tab;
            }
        }
    }
}