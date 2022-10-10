<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlNoTagNode.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Dom; 
///<summary>no tag definition</summary>
/**
* no tag definition 
*/
class HtmlNoTagNode extends HtmlNode{
    ///<summary></summary>
    /**
    * .ctr
    */
    public function __construct(){
        parent::__construct("igk:notagnode");
    }
    ///<summary></summary>
    /**
    * can render tag
    */
    public function getCanRenderTag(){ 
        return false;
    }    
}