<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlConfigContentNode.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Dom;

class HtmlConfigContentNode extends HtmlNode{
    protected $tagname = "div";
    protected function initialize()
    { 
        $this->setId("igk-cnf-content")->setClass("igk-cnf-content");
    } 
}