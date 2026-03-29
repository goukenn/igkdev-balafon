<?php
// @author: C.A.D. BONDJE DOUE
// @file: SvgListNode.php
// @date: 20221209 09:28:06
namespace IGK\System\Html\Dom;
/**
* svg list container
* @package IGK\System\Html\Dom
*/
class SvgListNode extends HtmlNode{
    /**
    * Name of tagname.
    * @var mixed
    */
    var $tagname = 'igk:svg-list';
    /**
    * Initializes.
    */
    protected function initialize()
    {
        parent::initialize();
        $this['class'] = 'igk-svg-lst'; 
    }
}