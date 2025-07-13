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
final class HtmlSingleNodeViewerNode extends HtmlNode{
    private $m_callback;
    var $targetNode;
    /**
     * 
     * @param array|mixed $v 
     * @return $this 
     */
    public function setContent($v){
        $this->targetNode->setContent(...func_get_args());
        return $this;
    }
    protected function _acceptRender($options = null):bool{
        if($this->targetNode)
            return $this->IsVisible;
        return false;
    }
    public function __construct($node, $callback=null){
        parent::__construct("igk:singleViewItem");
        if (is_string($node) ){
            $node = igk_create_node(trim($node));
        } 
        $this->targetNode=$node;
        $this->m_callback=$callback;
    }
    protected function _getRenderingChildren($option=null){
        return [$this->targetNode];
    }
    protected function __RenderComplete($options=null){ 
        igk_html_rm($this);
        if($this->m_callback){
            igk_invoke_callback_obj($this, $this->m_callback);
            $this->m_callback=null;
            unset($this->m_callback);
        }
        unset($this->targetNode);
    }
    protected function _addChild($item, $index=null){
        return false;
    }
    public function getCanRenderTag(){
        return false;
    }
}