<?php
// @author: C.A.D. BONDJE DOUE
// @file: IViewExpressArg.php
// @date: 20221018 10:23:22
namespace IGK\System\Runtime\Compiler\ViewCompiler;

/**
* auto generate doc.
* @package IGK\System\Runtime\Compiler
* @property string $value the value
*/
interface IViewExpressionArg{
    /**
    * Returns Expression.
    */
    function getExpression();
}