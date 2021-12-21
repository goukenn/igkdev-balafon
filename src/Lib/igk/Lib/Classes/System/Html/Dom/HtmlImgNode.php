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
    public function __construct($src=null){
        parent::__construct("igk-img");
        $this["src"] = $src; 
        $this["xmlns:igk"] = self::HTML_NAMESPACE;
    } 
    public function setSrc($source){
        $this["src"] = $source;
        return $this;
    }
    public function closeTag()
    {
        return true;
    }
    
    
}