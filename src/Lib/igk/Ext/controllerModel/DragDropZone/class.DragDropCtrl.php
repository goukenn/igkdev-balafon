<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.DragDropCtrl.php
// @date: 20220803 13:48:59
// @desc: 

//controller code class declaration
//file is a part of the controller tab list

use IGK\System\Html\Dom\HtmlNode;

/**
* Drag drop zone ctrl.
*/
abstract class DragDropZoneCtrl extends \IGK\Controllers\ControllerTypeBase
{

    /**
    * Returns Can Add Child.
    */
    public function getCanAddChild(){
		return false;
	}

    /**
    * Initializes Target Node.
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
* Drag drop zone item.
*/
class DragDropZoneItem extends HtmlNode
{

    /**
    * Property: script node.
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