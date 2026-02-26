<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlBalafonJSComponentNode.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Dom;

/**
* Html balafon jscomponent node.
* @package IGK\System\Html\Dom
*/
class HtmlBalafonJSComponentNode extends HtmlScriptNode{    
    /**
     * Constructor.
     *
     * @param bool $autoremove Whether the node should be automatically removed after rendering.
     */
    public function __construct(bool $autoremove=true){
        parent::__construct();    
        $this["type"] =  "text/balafon-component";;
        $this["autoremove"] = $autoremove;
        $this->setCallback("handleRender", "igk_html_callback_production_minifycontent");
    }

    /**
    * Accept render.
    * @param null|mixed $options
    * @return bool
    */
    protected function _acceptRender($options = null):bool
    {
        return parent::_acceptRender($options);
    }
}   