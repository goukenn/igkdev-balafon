<?php
namespace IGK\System\Html\Dom;

use IGK\Resources\ResourceData;

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
        $this["src"] = new ResourceData($src); 
        $this["xmlns:igk"] = self::HTML_NAMESPACE; 
    } 
    public function setSrc($source){
        if ($source==null){
            unset($this["src"]);
        }else {
            if (!($g = igk_getv($this, "src"))){
                $g = new ResourceData($source);
                $this["src"] = $g;
            }else {
                $g->setValue( $source );
            }
        }
        return $this;
    }
    public function getSrc(){
        return $this["src"]->value;
    }
    public function closeTag()
    {
        return true;
    }
    
    
}