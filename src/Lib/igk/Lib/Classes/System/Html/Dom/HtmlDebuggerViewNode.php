<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlDebuggerViewNode.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Dom;
use IGKEvents;

/**
* Html debugger view node.
* @package IGK\System\Html\Dom
*/
class HtmlDebuggerViewNode extends HtmlNode{
    /**
    * .ctr
    */
    public function __construct()
    {        
        parent::__construct("igk:debugger-view");
        parent::_Add(new HtmlHookNode(IGKEvents::HOOK_DEBUGGER_VIEW, null));
    }
    /**
    * Returns Can Render Tag.
    */
    public function getCanRenderTag()
    {
        return false;
    }
    /**
    * Returns Message.
    */
    public function getMessage(){
        return null;
    }
}