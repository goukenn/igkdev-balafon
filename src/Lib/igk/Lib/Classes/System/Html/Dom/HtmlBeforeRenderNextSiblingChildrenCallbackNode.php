<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlBeforeRenderNextSiblingChildrenCallbackNode.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Dom;
/**
* before next sibling, call configuration callback every time AcceptRender is called 
*/
final class HtmlBeforeRenderNextSiblingChildrenCallbackNode extends HtmlNode {
    /**
    * Listener: listener.
    * @var mixed
    */
    private $listener;
    /**
    * .ctr
    * @param callable $listener
    */
    public function __construct(callable $listener){
        parent::__construct();
        $this->listener = $listener;        
    }
    /**
    * Returns Can Add Childs.
    */
    public function getCanAddChilds()
    {
        return false;
    }
    /**
    * Returns Can Render Tag.
    */
    public function getCanRenderTag()
    {
        return false;
    }
    /**
    * Accept render.
    * @param null|mixed $options
    * @return bool
    */
    protected function _acceptRender($options = null):bool 
    {
        $b = $this->listener;
        $b($options);
        return false;
    }
}