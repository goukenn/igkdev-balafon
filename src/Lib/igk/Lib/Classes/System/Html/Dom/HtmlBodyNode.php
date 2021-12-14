<?php

namespace IGK\System\Html\Dom;

// @file: HtmlBodyNode.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021

class HtmlBodyNode extends HtmlNode{
    protected $tagname = "body";
    private $m_bodybox;
    public function getBodyBox(){
        if ($this->m_bodybox ===null){
            $this->m_bodybox = $this->add("div");
        }
        return $this->m_bodybox;
    }
    public function addBodyBox(){
        return $this->getBodyBox();
    }
}