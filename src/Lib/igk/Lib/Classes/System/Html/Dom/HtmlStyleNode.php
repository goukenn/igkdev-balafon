<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlStyleNode.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Dom;


class HtmlStyleNode extends HtmlNode{
    protected $tagname = "style";

    public function __construct(){
        parent::__construct();
        $this["type"] = "text/css";
    }
}