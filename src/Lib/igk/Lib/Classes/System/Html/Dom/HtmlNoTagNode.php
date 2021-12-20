<?php
namespace IGK\System\Html\Dom; 
///<summary>Represente class: IGKHtmlNoTagNode</summary>
/**
* no definition 
*/
class HtmlNoTagNode extends HtmlNode{
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