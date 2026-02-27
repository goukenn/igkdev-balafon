<?php
// @file: IGKHtmlSingleNodeViewer.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\Dom;

/**
* Html single node viewer node.
* @package IGK\System\Html\Dom
*/
final class HtmlSingleNodeViewerNode extends HtmlNode{

    /**
    * Callback handler for callback.
    * @var mixed
    */
    private $m_callback;

    /**
    * Property: target node.
    * @var mixed
    */
    var $targetNode;

    /**
    * auto generate doc.
    * @param array|mixed $v
    * @return $this
    */

    public function setContent($v){
        $this->targetNode->setContent(...func_get_args());
        return $this;
    }

    /**
    * Accept render.
    * @param null|mixed $options
    * @return bool
    */

    protected function _acceptRender($options = null):bool{
        if($this->targetNode)
            return $this->IsVisible;
        return false;
    }

    /**
    * .ctr
    * @param mixed $node
    * @param null|mixed $callback
    */
    public function __construct($node, $callback=null){
        parent::__construct("igk:singleViewItem");
        if (is_string($node) ){
            $node = igk_create_node(trim($node));
        } 
        $this->targetNode=$node;
        $this->m_callback=$callback;
    }

    /**
    * Get rendering children.
    * @param null|mixed $option
    */

    protected function _getRenderingChildren($option=null){
        return [$this->targetNode];
    }

    /**
    * Render complete.
    * @param null|mixed $options
    */

    protected function __RenderComplete($options=null){ 
        igk_html_rm($this);
        if($this->m_callback){
            igk_invoke_callback_obj($this, $this->m_callback);
            $this->m_callback=null;
            unset($this->m_callback);
        }
        unset($this->targetNode);
    }

    /**
    * Add child.
    * @param mixed $item
    * @param null|mixed $index
    */

    protected function _addChild($item, $index=null){
        return false;
    }

    /**
    * Returns Can Render Tag.
    */

    public function getCanRenderTag(){
        return false;
    }
}