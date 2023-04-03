<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlBeforeRenderNextSiblingChildrenCallbackNode.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Dom;
 

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
    protected function _acceptRender($options = null):bool 
    {
        $b = $this->listener;
        $b($options);
        return false;
    }
}