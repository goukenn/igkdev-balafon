<?php
// @author: C.A.D. BONDJE DOUE
// @file: IHtmlNodeEvaluableExpression.php
// @date: 20240122 13:01:49
namespace IGK\System\Html;
/**
* 
* @package IGK\System\Html
* @author C.A.D. BONDJE DOUE
*/
interface IHtmlNodeEvaluableExpression{

    /**
    * auto generate doc.
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