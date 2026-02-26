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
* auto generate doc.
*/
abstract class IGKCanvaZoneCtrl extends \IGK\Controllers\ControllerTypeBase
{
	private $m_canva;
	public function __construct(){
		parent::__construct();
	}

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
    * auto generate doc.
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
	 * 
	 * @return never 
	 * @throws IGKException 
	 * @throws ArgumentTypeNotValidException 
	 * @throws ReflectionException 
	 */
	public function getCanvaRendering(){
		//override this method to render on canvas
		//exit for rectangle
		//default canvas width : 300, height:150 . to change used canva.width and canva.height properties. value is an integer.
		igk_wl(IO::ReadAllText(dirname(__FILE__)."/".IGK_DATA_FOLDER."/context.iwcjs"));
		igk_exit();
	}
} 