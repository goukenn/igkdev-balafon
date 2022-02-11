<?php

namespace IGK\System\Html\Dom;

class HtmlConfigContentNode extends HtmlNode{
    protected $tagname = "div";
    protected function initialize()
    { 
        $this->setId("igk-cnf-content")->setClass("igk-cnf-content");
    } 
}