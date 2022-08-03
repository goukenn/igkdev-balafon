<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKType.php
// @date: 20220803 13:48:54
// @desc: 

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