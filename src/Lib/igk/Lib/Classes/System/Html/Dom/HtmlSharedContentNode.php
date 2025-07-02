<?php
// @file: IGKHtmlSharedContentNode.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Html\Dom;

final class HtmlSharedContentNode extends HtmlNode{
    private $m_ctrl;
    public function __construct($ctrl){
        parent::__construct("igk-shared-content");
        $this->m_ctrl=$ctrl;
    }
    protected function _getRenderingChildren($o=null){
        $t=array();
        $entities=$this->m_ctrl->getEntities();
        if($entities){
            foreach($entities as $v){
                if($v->IsVisible){
                    $t[]=$v;
                }
            }
        }
        return $t;
    }
    public function getIsRenderTagName(){
        return false;
    }
}
