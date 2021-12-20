<?php

namespace IGK\System\Html\Dom;

class HtmlScriptNode extends HtmlNode{
    protected $tagname = "script";
    public function __construct()
    {
        parent::__construct();
        $this["type"] = "text/javascript";
        $this["language"] = "javascript";
    }
    public function getCanAddChilds()
    {
        return false;
    }
}