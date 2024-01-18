<?php
// @author: C.A.D. BONDJE DOUE
// @file: IHtmlNodeBuilderVisitor.php
// @date: 20240118 07:50:37
namespace IGK\System\Html;

use IGK\System\Html\Dom\HtmlItemBase;

///<summary></summary>
/**
* handle node builder visitor
* @package IGK\System\Html
* @author C.A.D. BONDJE DOUE 
* @property ?string $fallbackTagName fallback tag name 
*/
interface IHtmlNodeBuilderVisitor{
    function setContext(?object $context);
    function getContext():?object;
    /**
     * setting up 
     * @param HtmlItemBase $node 
     * @param mixed $data 
     * @param mixed $last_child 
     * @return mixed 
     */
    function setup(HtmlItemBase $node, $data, & $last_child = null);
}