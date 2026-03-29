<?php
// @author: C.A.D. BONDJE DOUE
// @file: CopyrightExpression.php
// @date: 20230225 19:24:57
namespace IGK\Helper\Expressions;
/**
* auto generate doc.
* @package IGK\Helper\Expressions
*/
class CopyrightExpression extends ValueExpression{
    /**
    * Returns.
    * @param string $expression
    */
    public static function Get(string $expression){
        $exp = new static;
        $exp->data["%year%"] = date('Y');
        $exp->data["%copy%"] = "&copy;";
        return $exp->replace($expression);
    }
}