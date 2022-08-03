<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlBalafonJSComponentNode.php
// @date: 20220803 13:48:56
// @desc: 


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