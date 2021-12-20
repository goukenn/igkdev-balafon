<?php
// @file: IGKHtmlSharedContentNode.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Html\Dom;

final class HtmlSharedContentNode extends HtmlNode{
    private $m_ctrl;
    ///<summary></summary>
    ///<param name="ctrl"></param>
    public function __construct($ctrl){
        parent::__construct("igk-shared-content");
        $this->m_ctrl=$ctrl;
    }
    ///<summary></summary>
    ///<param name="o" default="null"></param>
    protected function __getRenderingChildren($o=null){
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
    ///<summary></summary>
    public function getIsRenderTagName(){
        return false;
    }
}
