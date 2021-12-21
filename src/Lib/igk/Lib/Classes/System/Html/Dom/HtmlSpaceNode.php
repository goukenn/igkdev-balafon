<?php
namespace IGK\System\Html\Dom; 
///<summary>Represente class: IGKHtmlNoTagNode</summary>
/**
* no definition 
*/
class HtmlSpaceNode extends HtmlNode{
    ///<summary></summary>
    /**
    * 
    */
    public function __construct(){
        parent::__construct("igk:space");
    }
    public function getContent(){
        return "&nbsp;";
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getCanRenderTag(){ 
        return false;
    }
    
}