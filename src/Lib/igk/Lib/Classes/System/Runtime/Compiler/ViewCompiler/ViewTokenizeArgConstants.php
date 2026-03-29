<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewTokenizeArgConstants.php
// @date: 20221021 09:36:15
namespace IGK\System\Runtime\Compiler\ViewCompiler;
use IGK\System\Runtime\Compiler\CompilerConstants;
/**
* auto generate doc.
* @package IGK\System\Runtime\Compiler\ViewCompiler
*/
abstract class ViewTokenizeArgConstants extends CompilerConstants{
    /**
    * Constant: setter var.
    * @var mixed
    */
    const SETTER_VAR = '$___IGK_PHP_SETTER_VAR___';
    /**
    * Constant: getter var.
    * @var mixed
    */
    const GETTER_VAR = '$___IGK_PHP_GETTER_VAR___';
    /**
    * Constant: expression.
    * @var mixed
    */
    const EXPRESSION = '$___IGK_PHP_EXPRESSION___'; // when depend on variables
    /**
    * Express eval getter.
    * @param string $v
    * @param null|array $dependOn
    */
    public static function ExpressEvalGetter(string $v, ?array $dependOn=null){
        $v = str_replace("'", "\\'", $v);
        if ($dependOn){
            $dependOn = ','.json_encode(array_keys($dependOn));
        }
        return self::GETTER_VAR . "[igk_express_eval('" . $v . "'".$dependOn.")]";
    }
}