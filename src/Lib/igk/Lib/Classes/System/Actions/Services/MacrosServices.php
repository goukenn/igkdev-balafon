<?php
// @author: C.A.D. BONDJE DOUE
// @file: MacrosServices.php
// @date: 20230106 00:56:46
namespace IGK\System\Actions\Services;


///<summary></summary>
/**
* 
* @package IGK\System\Actions\Services
*/
class MacrosServices{
    static $macros = [];

    /**
     * register action method 
     * @param string $name 
     * @param callable $func 
     * @return void 
     */
    public static function Register(string $name, callable $func){
        self::$macros[$name] = $func;
    }
    public static function GetFunc(string $name){
        return igk_getv(self::$macros, $name);
    }
}