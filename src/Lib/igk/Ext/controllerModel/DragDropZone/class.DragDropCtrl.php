<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.DragDropCtrl.php
// @date: 20220803 13:48:59
// @desc: 

//controller code class declaration
//file is a part of the controller tab list

use IGK\System\Html\Dom\HtmlNode;

abstract class DragDropZoneCtrl extends \IGK\Controllers\ControllerTypeBase
{
	public function getCanAddChild(){
		return false;
	}
 
	protected function initTargetNode():?HtmlNode
	{
		$t = new DragDropZoneItem();
		$t["id"]="dropzone";
		return $t;
	}
}

class DragDropZoneItem extends HtmlNode
{
	private $m_scriptNode;
	public function __construct(){
		parent::__construct("div");
		$this["class"]="role-drag-drop";
		$this->m_scriptNode =  HtmlNode::CreateWebNode("script");
		$this->m_scriptNode->Content = "ns_igk.winui.dragdrop.init(igk.getParentScript());";
		$this->add($this->m_scriptNode);
	}
} 