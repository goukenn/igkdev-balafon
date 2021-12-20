<?php

namespace IGK\System\Html\Dom;

use IGKEvents;

class HtmlDebuggerViewNode extends HtmlNode{
    public function __construct()
    {        
        parent::__construct("igk:debugger-view");
        parent::_Add(new HtmlHookNode(IGKEvents::HOOK_DEBUGGER_VIEW));
    }
    public function getCanRenderTag()
    {
        return false;
    }
    public function getMessage(){
        return null;
    }
}