<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlNoTagNode.php
// @date: 20220803 13:48:56
// @desc: 

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