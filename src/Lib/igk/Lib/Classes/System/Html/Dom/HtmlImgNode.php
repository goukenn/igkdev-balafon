<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlImgNode.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Dom;

use IGK\Resources\ResourceData;
use IGK\System\Html\HtmlAttributeArray;
use IGK\System\Html\HtmlResolvLinkValue;
use IGKException;

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
        $this->setSrc($src);
    } 
    protected function createAttributeArray(){ 
        return new HtmlAttributeArray([
            "src"=>new HtmlResolvLinkValue()
        ]);
    }
    /**
     * set image sources
     * @param mixed $source 
     * @return $this 
     * @throws IGKException 
     */
    public function setSrc($source){ 
        if (is_null($source)){
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
    /**
     * is self closing tag
     * @return bool 
     */
    public function closeTag():bool
    {
        return true;
    } 
}