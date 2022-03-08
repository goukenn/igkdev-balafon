<?php

namespace IGK\System\Html\Dom;

/**
 * represent a web widget
 * @package IGK\System\Html\Dom
 */
class HtmlWidgetNode extends HtmlNode{
    protected $tagname = "div";

    public function __construct($tagname=null){
        parent::__construct($tagname);
    }
    protected function initialize()
    {
        parent::initialize();
        $this["class"] = "widget";
    }
}