<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKHtmlColorPickerItem.php
// @date: 20220803 13:48:58
// @desc: 

//igk.winui.colorpicker

use IGK\Controllers\NonVisibleControllerBase;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Number;

/**
* auto generate doc.
*/
final class IGKHtmlColorPickerItem extends HtmlNode
{
	private $m_script;
	private $r;
	private $g;
	private $b;
	/**
	 * Returns the current color as a hex web color string (e.g. #rrggbb).
	 *
	 * @return string
	 */
	public function getWebValue(){
		$v_r = Number::ToBase($this->r, 16, 2);
		$v_g = Number::ToBase($this->g, 16, 2);
		$v_b = Number::ToBase($this->b, 16, 2);

		return "#".$v_r.$v_g.$v_b;
	}
	/**
	 * Constructor.
	 */
	public function __construct(){
		parent::__construct("div");
		$this->setClass("igk-clpicker");

		$this->div()
			->addTrackBar()->setId("clr");
		$this->div()
			->addTrackBar()->setId("clg");
		$this->div()
			->addTrackBar()->setId("clb");

		$frm = $this->div()->addForm();
		$frm->addInput("clValue", "hidden",$this->getWebValue());
		//$this->div()->Content = $this->getWebValue();


		$this->script()->Content =<<<EOF
ns_igk.readyinvoke('igk.winui.components.colorpicker.init');
EOF;

		include(dirname(__FILE__)."/Styles/".ConstantsEFAULT_THEME_STYLE);
	}

	/**
	 * Initialises the color picker in demo mode.
	 *
	 * @param mixed $t The demo context or target node.
	 * @return void
	 */
	public function initDemo($t){
		$this["demo"] = "1";
		$this->div()->Content = "for demo";

	}
}

/**
* auto generate doc.
*/
final class IGKHtmlCircleColorPickerItem extends HtmlNode
{
	private $m_script;
	private $m_ctrl;
	private $r;
	private $g;
	private $b;
	/**
	 * Returns the current color as a hex web color string (e.g. #rrggbb).
	 *
	 * @return string
	 */
	public function getWebValue(){
		$v_r = Number::ToBase($this->r, 16, 2);
		$v_g = Number::ToBase($this->g, 16, 2);
		$v_b = Number::ToBase($this->b, 16, 2);

		return "#".$v_r.$v_g.$v_b;
	}
	/**
	 * Constructor.
	 */
	public function __construct(){
		$this->m_ctrl = igk_getctrl("igkcolorpickercomponentcontroller");
		parent::__construct("div");
		$this->setClass("igk-circ-clpicker");
		$this->initView();

	}
	/**
	 * Builds the circle color picker view with image, trackbar, and input controls.
	 *
	 * @return void
	 */
	public function initView(){
		$this->clearChilds();
		$d = $this->div()->setClass("dispib");
		$c = $d->div();
		$uri = $this->m_ctrl->getDataDir()."/R/Img/bg-circ.png";
		$uri = IGKResourceUriResolver::getInstance()->resolve($uri); 
		$c->addImg()->setAttribute("src", $uri);

		$c->div()->setClass("posab loc_l loc_t loc_r fith igk-circ-pan")->setStyle("border :1px solid #eee");

		$d->div()->setClass("dispb alignc igk-circ-v")->Content = "&nbsp;";
		$d->div()->setClass("dispb")->addTrackbar();
		$i = $d->div()->setClass("dispb")->addInput("clvalue", "text");
		$i["class"]= "igk-form-control";

$this->script()->Content = <<<EOF
ns_igk.readyinvoke('igk.winui.components.circleColorPicker.init');
EOF;
	}

	/**
	 * Initialises the circle color picker in demo mode.
	 *
	 * @param mixed $t The demo context or target node.
	 * @return void
	 */
	public function initDemo($t){
		$this["demo"] = "1";
		$this->div()->setClass("demo")->Content = "for demo";
	}
}

/**
* auto generate doc.
*/
final class IGKColorPickerComponentController extends NonVisibleControllerBase
{
	/**
	 * Returns whether the resource can be modified.
	 *
	 * @return bool
	 */
	public function getcanModify(){return false;}
	/**
	 * Returns whether the resource can be deleted.
	 *
	 * @return bool
	 */
	public function getcanDelete(){return false;}
}