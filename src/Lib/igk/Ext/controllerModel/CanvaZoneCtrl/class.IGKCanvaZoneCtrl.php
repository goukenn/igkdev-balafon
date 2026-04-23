<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKCanvaZoneCtrl.php
// @date: 20220803 13:48:59
// @desc: 
use IGK\Controllers\BaseController;
use IGK\Helper\IO;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Html\Dom\CanvaZoneNode;
use IGK\System\Html\Dom\HtmlNode; 

/*
represent a canva zone controller type
*/
/**
* Igkcanva zone ctrl.
*/
abstract class IGKCanvaZoneCtrl extends \IGK\Controllers\ControllerTypeBase
{
    /**
    * Flag: canva.
    * @var mixed
    */
    private $m_canva;
    /**
    * .ctr
    */
    public function __construct(){
		parent::__construct();
	}
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
    protected function initTargetNode():?HtmlNode{
		$n = parent::initTargetNode();
		$this->m_canva = new CanvaZoneNode($this);
		$_id = igk_css_str2class_name( strtolower($this->getName()."_canva"));
		$this->m_canva->setId($_id);
		$this->m_canva["class"] = $_id;
		$n->add($this->m_canva);
		return $n;
	}
    /**
    * View.
    * @return BaseController
    */
    public function View():BaseController{
		if (!$this->IsVisible)
		{
			igk_html_rm($this->TargetNode);
		}
		return $this;
	}
    /**
    * auto generate doc.
    * @return never
    */
    public function getCanvaRendering(){
		igk_wl(IO::ReadAllText(dirname(__FILE__)."/".IGK_DATA_FOLDER."/context.iwcjs"));
		igk_exit();
	}
} 