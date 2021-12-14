<?php

namespace IGK\System\Html\Dom;

// @file: HtmlBodyNode.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021

class HtmlCommentNode extends HtmlItemBase{
    protected $tagname = "igk:comment";
    
    public function getCanAddChilds()
    {
        return false;
    }
    public function render($options=null){
        return "<!--" .$this->getContent(). " -->";
    }
}