<?php
namespace IGK\System\Html\Dom; 
///<summary>Represente class: IGKHtmlNoTagNodeItem</summary>
/**
* no definition 
*/
class HtmlNoTagNodeItem extends HtmlNode{
    ///<summary></summary>
    /**
    * 
    */
    public function __construct(){
        parent::__construct("igk:notagnode");
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getCanRenderTag(){ 
        return false;
    }
    
}