<?php
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
}