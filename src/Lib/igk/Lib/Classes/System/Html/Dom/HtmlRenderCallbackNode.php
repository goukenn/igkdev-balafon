<?php

// @author: C.A.D. BONDJE DOUE
// @filename: HtmlRenderCallbackNode.php
// @date: 20220428 06:45:18
// @desc: render callback node

namespace IGK\System\Html\Dom;


/**
 * on render callback call
 * @package IGK\System\Html\Dom
 */
class HtmlRenderCallbackNode extends HtmlNode{
    protected $tagname = "igk:render-callback-node";
    private $m_callbackobj;
    public function getCanRenderTag()
    {
        return false;
    }
    public function __construct($callbackobj)
    {
        parent::__construct();
        $this->m_callbackobj = $callbackobj;
    }

    protected function _acceptRender($options = null):bool
    {
        $param = [ $options ]; 
        return  igk_invoke_callback_obj($this, $this->m_callbackobj, $param); 
    }
}
