<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlSpaceNode.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Dom; 
///<summary>Represente class: IGKHtmlNoTagNode</summary>
/**
* no definition 
*/
class HtmlSpaceNode extends HtmlNode{
    ///<summary></summary>
    /**
    * 
    */
    public function __construct(){
        parent::__construct("igk:space");
    }
    public function getContent(){
        return "&nbsp;";
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getCanRenderTag(){ 
        return false;
    }
    
}