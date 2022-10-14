<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReadBlockEnvironment.php
// @date: 20221014 12:07:17
namespace IGK\System\Runtime\Compiler;


///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler
*/
class ReadBlockEnvironment{
    public static function IsExtract():bool{
        $c = igk_environment()->peek(ViewEnvironmentArgs::class."/compiler_args");  
        if ($c){

            $var = $c->variables;
            $extract = igk_getv($var, "___IGK_PHP_EXTRACT_VAR___", false);
            return $extract; 
        } 
        return false;
    }
}