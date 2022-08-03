<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlBarNode.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Dom;

class HtmlBarNode extends HtmlNode{
    protected $tagname ="span";

    protected function initialize()
    {   
        $this["class"] = "igk-bar";
    }
}