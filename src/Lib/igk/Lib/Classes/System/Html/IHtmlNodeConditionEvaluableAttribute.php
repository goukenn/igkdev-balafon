<?php
// @author: C.A.D. BONDJE DOUE
// @file: IHtmlNodeConditionEvaluableAttribute.php
// @date: 20240122 11:25:26
namespace IGK\System\Html;


///<summary></summary>
/**
* 
* @package IGK\System\Html
* @author C.A.D. BONDJE DOUE
*/
interface IHtmlNodeConditionEvaluableAttribute{
    function evaluate($context):bool;
}