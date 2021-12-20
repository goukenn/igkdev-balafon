<?php

namespace IGK\System\Html\Dom;


class HtmlBodyMainScript extends HtmlScriptNode{
    static $item;
    public static function getItem(){
        if (self::$item === null)
            self::$item = new self();
        return self::$item;
    }
    private function __construct(){
        parent::__construct();
        $this["class"] = "igk-mbody-script";
    }
     
    protected function __getRenderingChildren($options = null)
    {
        return [
            new HtmlBodyInitDocumentNode()
        ];
    }
}   