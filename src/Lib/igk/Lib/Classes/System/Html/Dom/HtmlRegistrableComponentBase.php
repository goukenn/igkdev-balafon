<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlRegistrableComponentBase.php
// @date: 20230307 07:20:54
namespace IGK\System\Html\Dom;
 
use IGK\System\Html\IHtmlRegistrableComponent;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Dom
*/
abstract class HtmlRegistrableComponentBase extends HtmlNode implements IHtmlRegistrableComponent{
    /**
     * set registrable component
     * @param mixed $doc 
     * @return void 
     */
    public abstract static function InitComponent($doc);
}