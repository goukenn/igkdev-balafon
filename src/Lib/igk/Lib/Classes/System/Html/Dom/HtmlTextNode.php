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
    function getCanRenderTag(){
        return false;
    }
    function getCanAddChilds()
    {
        return false;
    }
    public function getNodeType(){
        return XMLNodeType::TEXT;
    }
    ///<summary>.ctr</summary>
    public function __construct($content=""){
        parent::__construct();
        $this->content = $content;
    }
    public function render($options=null){
        return $this->content; 
    }
    public function setContent($value){
        $this->content = $value;
    }
}