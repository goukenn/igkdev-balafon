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
    * auto generate doc.
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
    * auto generate doc.
    */
    public function getCanAddChilds()
    {
        return false;
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