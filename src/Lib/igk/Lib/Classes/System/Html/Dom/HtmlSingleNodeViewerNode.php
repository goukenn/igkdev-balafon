<?php
// @file: IGKHtmlSingleNodeViewer.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
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
    

    ///<summary></summary>
    ///<param name="options" default="null"></param>
    protected function _acceptRender($options = null):bool{
        if($this->targetNode)
            return $this->IsVisible;
        return false;
    }
    ///<summary>.ctr</summary>
    ///<param name="node">.node to render once</param>
    ///<param name="callback">call after render</param>
    public function __construct($node, $callback=null){
        parent::__construct("igk:singleViewItem");
        if (is_string($node) ){
            $node = igk_create_node(trim($node));
        } 
        $this->targetNode=$node;
        $this->m_callback=$callback;
    }
    ///<summary></summary>
    ///<param name="option" default="null"></param>
    protected function _getRenderingChildren($option=null){
        
        return [$this->targetNode];
    }
 
    ///<summary></summary>
    ///<param name="options" default="null"></param>
    protected function __RenderComplete($options=null){ 
    
        igk_html_rm($this);
        if($this->m_callback){
            igk_invoke_callback_obj($this, $this->m_callback);
            $this->m_callback=null;
            unset($this->m_callback);
        }
        unset($this->targetNode);
    }
    ///<summary></summary>
    ///<param name="item"></param>
    ///<param name="index" default="null"></param>
    protected function _addChild($item, $index=null){
        return false;
    }
    ///<summary></summary>
    public function getCanRenderTag(){
        return false;
    }
}
