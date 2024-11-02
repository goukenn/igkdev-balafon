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
    protected $tagname = 'igk:notagnode';
    ///<summary></summary>
    /**
    * .ctr
    */
    public function __construct(){       
        parent::__construct();
    }
    ///<summary></summary>
    /**
    * can render tag
    */
    public function getCanRenderTag(){ 
        return false;
    }    
    public function getIsActive(){}
}