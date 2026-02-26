<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.slider.php
// @date: 20220803 13:48:58
// @desc: 


use IGK\System\Html\Dom\HtmlComponentNode;
use IGK\System\Html\Dom\HtmlNode;

/**
* Igkhtml slider zone.
*/
final class IGKHtmlSliderZone extends HtmlNode{

    /**
    * Returns Can Render Tag.
    */
    function getCanRenderTag(){return false; }

    /**
    * .ctr
    */
    public function __construct(){
		parent::__construct("igk-slider-zone");
	}
}

/**
* Igkhtml slider item.
*/
class IGKHtmlSliderItem extends HtmlComponentNode{

	/** @var HtmlNode*/
	private $m_content;

    /**
    * Property: script.
    * @var mixed
    */
    private $m_script;

    /**
    * Property: orientation.
    * @var mixed
    */
    private $m_orientation;

    /**
    * Returns Orientation.
    */

    public function getOrientation(){return $this->m_orientation; }

    /**
    * Sets Orientation.
    * @param mixed $v
    */

    public function setOrientation($v){ $this->m_orientation = $v; return $this; }

    /**
    * .ctr
    */
    public function __construct(){
		parent::__construct("div");		
	}

    /**
    * Initializes.
    */

    protected function initialize()
	{
		parent::initialize();
		$this["class"]="igk-slider";
		$this->m_orientation='horizontal'; //'vertical';
		$this->m_content = parent::add(new IGKHtmlSliderZone());
		$this->m_content["class"] = "igk-slider-c";
		$this->m_script = parent::addScript();
	}

    /**
    * Adds Page.
    * @param mixed $n
    */

    public function addPage($n){
		$dv = $this->m_content->div();
		$dv["class"] = "igk-slider-page";
		$dv->add($n);
		return $dv;
	}

    /**
    * Clears Childs.
    */

    public function ClearChilds(){
		$this->m_content->clearChilds();
	}

    /**
    * Initializes Demo.
    * @param mixed $t
    */

    public function initDemo($t){
		$this->clearChilds();
		$this->addPage(igk_create_node("div")->setContent("page1"));
		$this->addPage(igk_create_node("div")->setContent("page2"));
		$this->addPage(igk_create_node("div")->setContent("page3"));
	}

    /**
    * Accept render.
    * @param null|mixed $options
    * @return bool
    */

    protected function _acceptRender($options = null):bool {
		$this->m_script->setIsVisible(false);
		// $this->m_script->Content = "igk.winui.slider.init({orientation:'{$this->m_orientation}'})";
		return true;
	}
}
