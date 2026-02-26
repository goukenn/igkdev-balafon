<?php
// @file: IGKDebugCtrl.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\Controllers;
use IGK\Server;
use IGK\System\Html\Dom\HtmlDebuggerViewNode; 
use IGK\System\Html\HtmlUtils;

/**
* auto generate doc.
* @package IGK\Controllers
*/
final class DebugController extends BaseController{
    /**
     * Returns the name identifier for the debug controller.
     *
     * @return string
     */
    public function getName(): string{
        return IGK_DEBUG_CTRL;
    }
    /**
     * Adds a message node to the debug top div.
     *
     * @param mixed $div
     * @return void
     */
    public function addMessage($div){
        $this->m_topdiv->add($div);
    }
    /**
     * Clears all child nodes from the debug display area.
     *
     * @return void
     */
    public function ClearDebug(){
        $this->m_topdiv->clearChilds();
    }
    /**
     * Returns the singleton debugger view node instance.
     *
     * @return HtmlDebuggerViewNode
     */
    public function getDebuggerView(){
        static $debug=null;
        if($debug === null){
            $debug= new HtmlDebuggerViewNode();
        }
        return $debug;
    }
    /**
     * Returns whether the debug controller is visible on the current host.
     *
     * @return bool
     */
    public function getIsVisible():bool{
        return Server::IsLocal();
    }
    /**
     * Initialises the target node structure for the debug controller.
     *
     * @return ?\IGK\System\Html\Dom\HtmlNode
     */
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
    /**
     * Renders the debug controller into the debug zone or removes it when not visible.
     *
     * @return BaseController
     */
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