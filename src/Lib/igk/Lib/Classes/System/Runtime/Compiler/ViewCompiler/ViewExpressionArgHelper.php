<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewExpressionArgHelper.php
// @date: 20221015 12:12:02
namespace IGK\System\Runtime\Compiler\ViewCompiler;

use IGK\System\ViewEnvironmentArgs;
use IGKException;

///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler
*/
abstract class ViewExpressionArgHelper{
    /**
     * define setter object variable : new ViewExpressionSetter($vars);
     */
    const SETTER_VAR = '___IGK_PHP_SETTER_VAR___';
    const GETTER_VAR = '___IGK_PHP_GETTER_VAR___';
    const EXPRESSION = '___IGK_PHP_EXPRESSION___';
    const RESPONSE = '___IGK_PHP_RESPONSE___';

    public static $Variables = [];    
    
    /**
     * get variable property
     * @param string $name 
     * @return mixed 
     * @throws IGKException 
     */
    public static function GetVar(string $name){
        $c = igk_array_peek_last(self::$Variables); 
        if ($c){
            $var = $c->variables; 
            $setter = igk_getv($var, self::SETTER_VAR);
            if ($name===self::SETTER_VAR){
                return $setter;
            }
            if ( ($setter = igk_getv($var, self::SETTER_VAR))->contains($name)){ 
                return $setter[$name];
            } 
            return igk_getv($var, $name);
        }
        return null;
    }
}