<?php

namespace IGK\System\Html\Dom;

class HtmlBodyInitDocumentNode extends HtmlNode{
    public function getCanAddChilds()
    {
        return false;
    }
    public function render($options=null){ 
        return  " if(window.ns_igk)ns_igk.init_document();";
    }
}