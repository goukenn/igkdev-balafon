<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlNoTagNode.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Dom; 
/**
* no tag definition 
*/
class HtmlNoTagNode extends HtmlNode{
    protected $tagname = 'igk:notagnode';
    /**
    * .ctr
    */
    public function __construct(){       
        parent::__construct();
    }
    /**
    * can render tag
    */
    public function getCanRenderTag(){ 
        return false;
    }    
    public function getIsActive(){}
}