<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlTableNode.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Dom;


class HtmlTableNode extends HtmlNode{
    protected $tagname = "table";

    public function __construct(){
        parent::__construct(); 
        $this["class"] = "igk-table";
    }
}