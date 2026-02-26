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
    * auto generate doc.
    */
    function getCanRenderTag(){
        return false;
    }

    /**
    * auto generate doc.
    */
    function getCanAddChilds()
    {
        return false;
    }

    /**
    * auto generate doc.
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
    * auto generate doc.
    * @param null|mixed $options
    */
    public function render($options=null){
        return $this->content; 
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */
    public function setContent($value){
        $this->content = $value;
    }
}