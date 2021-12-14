<?php

namespace IGK\System\Html\Dom;

use IGK\System\XML\XMLNodeType;

/**
 * reprensent text done
 */
class HtmlTextNode extends HtmlItemBase{    
    function getCanRenderTag(){
        return false;
    }
    public function getNodeType(){
        return XMLNodeType::TEXT;
    }
    ///<summary>.ctr</summary>
    public function __construct($content=""){
        $this->content = $content;
    }
    public function render($options=null){
        return $this->content; 
    }
}