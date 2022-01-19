<?php

namespace IGK\System\Html\Dom;

class HtmlBarNode extends HtmlNode{
    protected $tagname ="span";

    protected function initialize()
    {   
        $this["class"] = "igk-bar";
    }
}