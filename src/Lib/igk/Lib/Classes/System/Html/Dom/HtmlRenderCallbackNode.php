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
    /**
    * Name of tagname.
    * @var mixed
    */
    protected $tagname = "igk:render-callback-node";
    /**
    * Callback handler for callbackobj.
    * @var mixed
    */
    private $m_callbackobj;
    /**
    * Returns Can Render Tag.
    */
    public function getCanRenderTag(){
        return false;
    }
    /**
    * .ctr
    * @param mixed $callbackobj
    */
    public function __construct($callbackobj){
        parent::__construct();
        $this->m_callbackobj = $callbackobj;
    }
    /**
    * Accept render.
    * @param null|mixed $options
    * @return bool
    */
    protected function _acceptRender($options = null):bool
    {
        $param = [ $options ]; 
        return igk_invoke_callback_obj($this, $this->m_callbackobj, $param) ? true: false;
    }
}