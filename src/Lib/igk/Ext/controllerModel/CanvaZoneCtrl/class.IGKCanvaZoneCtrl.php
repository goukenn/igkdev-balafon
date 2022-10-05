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
abstract class IGKCanvaZoneCtrl extends \IGK\Controllers\ControllerTypeBase
{
	private $m_canva;
	public function __construct(){
		parent::__construct();
	}
	protected function initComplete($context=null){
		parent::initComplete();
		igk_die("initialize canvas node js");
		// igk_js_load_script($this->App->Doc, dirname(__FILE__)."/".IGK_SCRIPT_FOLDER);
	}
	public function getCanAddChild(){
		return false;
	}
	protected function initTargetNode():?HtmlNode{
		$n = parent::initTargetNode();
		$this->m_canva = new CanvaZoneNode($this);
		$_id = igk_css_str2class_name( strtolower($this->getName()."_canva"));
		$this->m_canva->setId($_id);
		$this->m_canva["class"] = $_id;
		$n->add($this->m_canva);
		return $n;
	}
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