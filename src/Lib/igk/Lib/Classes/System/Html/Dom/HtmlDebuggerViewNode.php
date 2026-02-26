<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlDebuggerViewNode.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Dom;
use IGKEvents;

/**
* auto generate doc.
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
    * auto generate doc.
    */

    public function getCanRenderTag()
    {
        return false;
    }

    /**
    * auto generate doc.
    */

    public function getMessage(){
        return null;
    }
}