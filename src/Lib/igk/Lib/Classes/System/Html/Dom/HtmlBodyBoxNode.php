<?php
namespace IGK\System\Html\Dom;
use IGKEvents;
// @file: HtmlBodyNode.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2021

/**
* Html body box node.
* @package IGK\System\Html\Dom
*/
class HtmlBodyBoxNode extends HtmlNode{
    /**
    * Name of tagname.
    * @var mixed
    */
    protected $tagname = "div";
    /**
     * Constructor.
     * @param HtmlNode $parent The parent HTML node to attach this box to.
     */
    public function __construct(HtmlNode $parent)
    {
        $this->m_parent = $parent;
        parent::__construct();
        $this["class"] = "igk-bodybox fit igk-parentscroll igk-powered-viewer overflow-y-a";
    }
}