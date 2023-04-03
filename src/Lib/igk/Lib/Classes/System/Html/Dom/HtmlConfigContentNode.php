<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlConfigContentNode.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Dom;

/**
 * represent configuration node
 * @package IGK\System\Html\Dom
 */
class HtmlConfigContentNode extends HtmlNode{
    public $tagname = "div";
    protected function initialize()
    { 
        parent::initialize();
        $this->setId("igk-cnf-content")->setClass("igk-cnf-content");
    } 
    public function remove()
    {
        igk_wln_e("remove content .... ");
    }
}