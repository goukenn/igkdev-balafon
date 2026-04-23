<?php
// @author: C.A.D. BONDJE DOUE
// @file: IHtmlTemplateAttribute.php
// @date: 20221231 13:50:54
namespace IGK\System\Html;

/**
* a template attribute expression
* @package IGK\System\Html
*/
interface IHtmlTemplateAttribute{
    /**
    * Expression.
    * @return string
    */
    function expression():string;
}