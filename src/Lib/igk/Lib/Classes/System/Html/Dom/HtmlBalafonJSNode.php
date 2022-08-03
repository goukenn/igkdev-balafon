<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlBalafonJSNode.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Dom;


class HtmlBalafonJSNode extends HtmlScriptNode{    
    public function __construct(bool $autoremove=true){
        parent::__construct();    
        $this["type"] = "text/balafonjs";
        $this["autoremove"] = $autoremove;
        $this->setCallback("handleRender", "igk_html_callback_production_minifycontent");
    } 
}   