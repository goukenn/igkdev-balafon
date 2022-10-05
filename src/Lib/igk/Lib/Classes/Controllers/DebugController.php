<?php
// @file: IGKDebugCtrl.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\Controllers;

use IGK\Server;
use IGK\System\Html\Dom\HtmlDebuggerViewNode; 
use IGK\System\Html\HtmlUtils; 
 

final class DebugController extends BaseController{
    public function getName(){
        return IGK_DEBUG_CTRL;
    }
    ///<summary></summary>
    ///<param name="div"></param>
    public function addMessage($div){
        $this->m_topdiv->add($div);
    }
    ///<summary></summary>
    public function ClearDebug(){
        $this->m_topdiv->clearChilds();
    }
    ///<summary></summary>
    public function getDebuggerView(){
        static $debug=null;  
        if($debug === null){
            $debug= new HtmlDebuggerViewNode();
        }
        return $debug;
    }
    ///<summary></summary>
    public function getIsVisible():bool{
        return Server::IsLocal();
    }
    ///<summary></summary>
    protected function initTargetNode(): ?\IGK\System\Html\Dom\HtmlNode{
        $node=parent::initTargetNode();
        $cl=strtolower($this->getName());
        $node["class"]=$cl." loc_t loc_l zback posr";
        $node->add("h2")->Content=__("title.debugger");
        $this->m_topdiv=$node->add("div", array("class"=>$cl."_content"));
        $this->m_optionsdiv=$node->add("div", array("class"=>$cl."_options posr loc_b loc_l"));
        HtmlUtils::AddBtnLnk($this->m_optionsdiv, "btn.ClearDebug", $this->getUri("ClearDebug"));
        return $node;
    }
    ///<summary>debug ctrl view override</summary>
    public function View():BaseController{
        if($this->getIsVisible()){
            $body=igk_sys_debugzone_ctrl();
            if($body != null){
                $body->getTargetNode()->add($this->getTargetNode());
            }
        }
        else
            $this->getTargetNode()->remove();
        return $this;
    }
}
