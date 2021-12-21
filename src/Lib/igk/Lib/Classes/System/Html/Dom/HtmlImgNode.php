<?php
namespace IGK\System\Html\Dom; 
///<summary>Represente class: IGKHtmlNoTagNode</summary>
/**
* no definition 
*/
class HtmlImgNode extends HtmlNode{
    ///<summary></summary>
    /**
    * 
    */
    public function __construct(){
        parent::__construct("igk:img");
    }     
    public function closeTag()
    {
        return true;
    }
    
    
}