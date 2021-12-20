<?php

namespace IGK\System\Html\Dom;


class HtmlStyleNode extends HtmlNode{
    protected $tagname = "style";

    public function __construct(){
        parent::__construct();
        $this["type"] = "text/css";
    }
}