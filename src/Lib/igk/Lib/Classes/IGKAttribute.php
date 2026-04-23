<?php
// @file: IGKAttribute.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

/**
* Igkattribute.
*/
class IGKAttribute extends IGKObject{
    /**
    * Property: class attributes.
    * @var mixed
    */
    static $classAttributes=array();
    /**
    * .ctr
    */
    public function __construct(){    }
    /**
    * Returns Attributes.
    * @param mixed $classOrObject
    */
    public static function GetAttributes($classOrObject){
        $n=null;
        if(is_string($classOrObject)){
            $n=$classOrObject;
        }
        else
            $n=get_class($classOrObject);
        return igk_getv(self::$classAttributes, $n);
    }
    /**
    * Registers.
    * @param mixed $classname
    * @param mixed $attribute
    * @param mixed $allowmultiple
    * @param mixed $inherits
    */
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