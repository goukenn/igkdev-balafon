<?php
namespace IGK\System\Html\Dom;


class HtmlTableNode extends HtmlNode{
    protected $tagname = "table";

    public function __construct(){
        parent::__construct(); 
        $this["class"] = "igk-table";
    }
}