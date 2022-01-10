<?php
namespace IGK\System\Html\Dom;

use IGK\Resources\ResourceData;
use IGK\System\Html\HtmlAttributeArray;
use IGK\System\Html\HtmlResolvLinkValue;

///<summary>Represente class: IGKHtmlNoTagNode</summary>
/**
* no definition 
*/
class HtmlImgNode extends HtmlNode{
    ///<summary></summary>
    /**
    * @param string $src uri
    */
    public function __construct($src=null){
        parent::__construct("igk-img");
        $this["src"] = $src; 
        $this["xmlns:igk"] = self::HTML_NAMESPACE; 
    } 
    protected function createAttributeArray(){ 
        return new HtmlAttributeArray([
            "src"=>new HtmlResolvLinkValue()
        ]);
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