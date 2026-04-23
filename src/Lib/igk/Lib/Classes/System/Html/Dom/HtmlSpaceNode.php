<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlSpaceNode.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Dom; 

/**
* no definition 
*/
class HtmlSpaceNode extends HtmlNode{
    /**
    * auto generate doc.
    */    public function __construct(){
        parent::__construct("igk:space");
    }
    /**
    * Returns Content.
    */
    public function getContent(){
        return "&nbsp;";
    }
    /**
    * auto generate doc.
    */
    public function getCanRenderTag(){ 
        return false;
    }
}