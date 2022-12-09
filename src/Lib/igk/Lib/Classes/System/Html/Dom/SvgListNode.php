<?php
// @author: C.A.D. BONDJE DOUE
// @file: SvgListNode.php
// @date: 20221209 09:28:06
namespace IGK\System\Html\Dom;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Dom
*/
class SvgListNode extends HtmlNode{
    var $tagname = 'igk:svg-list';
    protected function initialize()
    {
        // igk_create_node("div")->setClass("igk-svg-lst")->setStyle("display:none;");
        $this['class'] = 'igk-svg-lst';

    }
}