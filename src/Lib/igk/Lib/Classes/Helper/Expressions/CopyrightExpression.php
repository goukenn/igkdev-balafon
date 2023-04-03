<?php
// @author: C.A.D. BONDJE DOUE
// @file: CopyrightExpression.php
// @date: 20230225 19:24:57
namespace IGK\Helper\Expressions;


///<summary></summary>
/**
* 
* @package IGK\Helper\Expressions
*/
class CopyrightExpression extends ValueExpression{
    public static function Get(string $expression){
        $exp = new static;
        $exp->data["%year%"] = date('Y');
        $exp->data["%copy%"] = "&copy;";
        return $exp->replace($expression);
    }
}