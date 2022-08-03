<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlBodyInitDocumentNode.php
// @date: 20220803 13:48:56
// @desc: 


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