<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.DragDropCtrl.php
// @date: 20220803 13:48:59
// @desc: 

//controller code class declaration
//file is a part of the controller tab list

use IGK\System\Html\Dom\HtmlNode;

/**
* auto generate doc.
*/
abstract class DragDropZoneCtrl extends \IGK\Controllers\ControllerTypeBase
{

    /**
    * auto generate doc.
    */
    public function getCanAddChild(){
		return false;
	}

    /**
    * auto generate doc.
    * @return ?HtmlNode
    */
    protected function initTargetNode():?HtmlNode
	{
		$t = new DragDropZoneItem();
		$t["id"]="dropzone";
		return $t;
	}
}

/**
* auto generate doc.
*/
class DragDropZoneItem extends HtmlNode
{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_scriptNode;

    /**
    * .ctr
    */
    public function __construct(){
		parent::__construct("div");
		$this["class"]="role-drag-drop";
		$this->m_scriptNode =  HtmlNode::CreateWebNode("script");
		$this->m_scriptNode->Content = "ns_igk.winui.dragdrop.init(igk.getParentScript());";
		$this->add($this->m_scriptNode);
	}
} 