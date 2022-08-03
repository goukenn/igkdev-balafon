<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlFormTitleNode.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Dom;


///<summary>Represente class: IGKHTmlFormTitle</summary>
/**
* Represente IGKHTmlFormTitle class
*/
final class HtmlFormTitleNode extends HtmlNode{
    ///<summary></summary>
    /**
    * 
    */
    public function __construct(){
        parent::__construct("div");
        $this["class"]="title";
    }
    ///<summary></summary>
    ///<param name="options" default="null"></param>
    /**
    * 
    * @param mixed $options the default value is null
    */
    public function AcceptRender($options=null){
        if(!$this->IsVisible){
            return 0;
        }
        $c=$this->Content;
        if($c || ($this->getChildCount()>0))
            return 1;
        return 0;
    }
}