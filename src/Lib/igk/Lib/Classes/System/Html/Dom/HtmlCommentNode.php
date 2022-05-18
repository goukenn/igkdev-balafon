<?php

namespace IGK\System\Html\Dom;

// @file: HtmlBodyNode.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021

class HtmlCommentNode extends HtmlItemBase{
    protected $tagname = "igk:comment";
    
    public function __construct(?string $data = null)
    {
        $this->setContent($data);
    }
    public function getCanAddChilds(){
        return false;
    }
    public function render($options=null){ 
        if (igk_getv($options, "NoComment"))
            return null;        
        return "<!-- " .trim($this->getContent()). " -->";
    }
    
}