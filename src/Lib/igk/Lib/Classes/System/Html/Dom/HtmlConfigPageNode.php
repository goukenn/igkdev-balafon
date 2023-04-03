<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlConfigPageNode.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Dom;

/**
 * represent configuration node
 * @package IGK\System\Html\Dom
 */
class HtmlConfigPageNode extends HtmlNode{
    public $tagname = "div";
    protected function initialize()
    { 
        parent::initialize();
        $this->setAttribute("class", "igk-cnf-page fit igk-parentscroll igk-powered-viewer overflow-y-a");
    } 
   
    public function remove()
    {
        igk_wln_e("try remove... ");
    }
    protected function _acceptRender($options = null): bool
    { 
        return true;
    } 
}