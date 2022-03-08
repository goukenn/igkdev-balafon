<?php

namespace IGK\System\Html\Dom;

use IGK\System\Html\HtmlContext;
use IGK\System\Html\HtmlReader;
use IGK\System\Html\HtmlUtils;
use IGK\XML\XMLNodeType;

///<summary>before next sibling</summary>
/**
* before next sibling, call configuration callback every time AcceptRender is called 
*/
final class HtmlBeforeRenderNextSiblingChildrenCallbackNode extends HtmlNode {
    private $listener;

    public function __construct(callable $listener){
        parent::__construct();
        $this->listener = $listener;        
    }
    public function getCanAddChilds()
    {
        return false;
    }
    public function getCanRenderTag()
    {
        return false;
    }
    public function AcceptRender($options = null)
    {
        $b = $this->listener;
        $b($options);
        return false;
    }
}