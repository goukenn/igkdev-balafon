<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlTextNode.php
// @date: 20220309 08:58:17
// @desc: text node
namespace IGK\System\Html\Dom;
use IGK\XML\XMLNodeType;
/**
 * represent text done
 */
class HtmlTextNode extends HtmlItemBase{
    /**
    * Returns Can Render Tag.
    */
    function getCanRenderTag(){
        return false;
    }
    /**
    * Returns Can Add Childs.
    */
    function getCanAddChilds()
    {
        return false;
    }
    /**
    * Returns Node Type.
    */
    public function getNodeType(){
        return XMLNodeType::TEXT;
    }
    /**
    * .ctr
    * @param mixed $content
    */
    public function __construct($content=""){
        parent::__construct();
        $this->content = $content;
    }
    /**
    * Renders.
    * @param null|mixed $options
    */
    public function render($options=null){
        return $this->content; 
    }
    /**
    * Sets Content.
    * @param mixed $value
    */
    public function setContent($value){
        $this->content = $value;
    }
}