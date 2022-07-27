<?php
// @author: C.A.D. BONDJE DOUE
// @file: IHtmlContextContainer.php
// @date: 20220706 22:26:41
namespace IGK\System\Html;


///<summary></summary>
/**
* return the context of this item
* @package IGK\System\Html
*/
interface IHtmlContextContainer{
    /**
     * get the context definition 
     * @return ?HtmlLoadingContext 
     */
    function getContext(): ?HtmlLoadingContext;
}