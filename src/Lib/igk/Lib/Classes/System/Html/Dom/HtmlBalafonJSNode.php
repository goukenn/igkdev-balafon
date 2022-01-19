<?php

namespace IGK\System\Html\Dom;


class HtmlBalafonJSNode extends HtmlScriptNode{    
    public function __construct(bool $autoremove=true){
        parent::__construct();    
        $this["type"] = "text/balafonjs";
        $this["autoremove"] = $autoremove;
        $this->setCallback("handleRender", "igk_html_callback_production_minifycontent");
    } 
}   