<?php
// @author: C.A.D. BONDJE DOUE
// @file: IHtmlNodeEvaluableExpression.php
// @date: 20240122 13:01:49
namespace IGK\System\Html;
/**
* auto generate doc.
* @package IGK\System\Html
* @author C.A.D. BONDJE DOUE
*/
interface IHtmlNodeEvaluableExpression{
    /**
    * Returns Value.
    * @return ?string
    */
    function getValue():?string;
    /**
     * evalue expression in context
     * @param mixed|array $context 
     * @return mixed 
     */
    function evaluate($context);
}