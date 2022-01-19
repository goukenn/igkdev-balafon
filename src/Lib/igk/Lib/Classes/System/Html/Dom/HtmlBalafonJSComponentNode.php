<?php

namespace IGK\System\Html\Dom;


class HtmlBalafonJSComponentNode extends HtmlScriptNode{    
    public function __construct(bool $autoremove=true){
        parent::__construct();    
        $this["type"] =  "text/balafon-component";;
        $this["autoremove"] = $autoremove;
        $this->setCallback("handleRender", "igk_html_callback_production_minifycontent");
    } 
    protected function __AcceptRender($options = null)
    {
        return parent::__AcceptRender($options);
    }
}   