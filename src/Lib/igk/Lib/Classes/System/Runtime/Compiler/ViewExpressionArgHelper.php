<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewExpressionArgHelper.php
// @date: 20221015 12:12:02
namespace IGK\System\Runtime\Compiler;

use IGK\System\ViewEnvironmentArgs;

///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler
*/
abstract class ViewExpressionArgHelper{
    const SETTER_VAR = '___IGK_PHP_SETTER___';
    public static function Key(){
        return ViewEnvironmentArgs::class."/compiler_args";
    }
    public static function GetVar(string $name){
        $c = igk_environment()->peek(self::Key());  
        if ($c){
            $var = $c->variables; 
            $setter = igk_getv($var, self::SETTER_VAR);
            if ($name===self::SETTER_VAR){
                return $setter;
            }
            if ( ($setter = igk_getv($var, self::SETTER_VAR))->contains($name)){
                // igk_wln_e("contains .....".$name);
                return $setter[$name];
            }
            igk_wln(__FILE__.":".__LINE__,  array_keys($var), $name, $setter );
            return igk_getv($var, $name);
        }
        return null;
    }
}