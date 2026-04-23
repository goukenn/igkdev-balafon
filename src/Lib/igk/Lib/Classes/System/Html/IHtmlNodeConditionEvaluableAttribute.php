<?php
// @author: C.A.D. BONDJE DOUE
// @file: IHtmlNodeConditionEvaluableAttribute.php
// @date: 20240122 11:25:26
namespace IGK\System\Html;

/**
* auto generate doc.
* @package IGK\System\Html
* @author C.A.D. BONDJE DOUE
*/
interface IHtmlNodeConditionEvaluableAttribute{
    /**
    * Evaluate.
    * @param mixed $context
    * @return bool
    */
    function evaluate($context):bool;
}