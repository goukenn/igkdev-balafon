<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlFormTitleNode.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Dom;
/**
* Represent IGKHTmlFormTitle class
*/
final class HtmlFormTitleNode extends HtmlNode{

    /**
    * auto generate doc.
    */    public function __construct(){
        parent::__construct("div");
        $this["class"]="title";
    }

    /**
    * auto generate doc.
    * @param mixed $options the default value is null
    */
    protected function _acceptRender($options = null):bool{
        if(!$this->IsVisible){
            return 0;
        }
        $c=$this->Content;
        if($c || ($this->getChildCount()>0))
            return 1;
        return 0;
    }
}