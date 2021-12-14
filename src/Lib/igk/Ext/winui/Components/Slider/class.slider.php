<?php

use IGK\System\Html\Dom\HtmlComponentNode;
use IGK\System\Html\Dom\HtmlNode;

final class IGKHtmlSliderZone extends HtmlNode{
	function getCanRenderTag(){return false; }
	public function __construct(){
		parent::__construct("igk-slider-zone");
	}
}
class IGKHtmlSliderItem extends HtmlComponentNode{

	/** @var HtmlNode*/
	private $m_content;
	private $m_script;
	private $m_orientation;


	public function getOrientation(){return $this->m_orientation; }
	public function setOrientation($v){ $this->m_orientation = $v; return $this; }

	public function __construct(){
		parent::__construct("div");
		$this["class"]="igk-slider";
		$this->m_orientation='horizontal'; //'vertical';
		$this->m_content = parent::add(new IGKHtmlSliderZone());
		$this->m_content["class"] = "igk-slider-c";
		$this->m_script = parent::addScript();
	}
	public function addPage($n){
		$dv = $this->m_content->addDiv();
		$dv["class"] = "igk-slider-page";
		$dv->add($n);
		return $dv;
	}
	public function ClearChilds(){
		$this->m_content->ClearChilds();
	}
	public function initDemo($t){
		$this->ClearChilds();
		$this->addPage(igk_createnode("div")->setContent("page1"));
		$this->addPage(igk_createnode("div")->setContent("page2"));
		$this->addPage(igk_createnode("div")->setContent("page3"));
	}
	public function AcceptRender($options=null){
		$this->m_script->Content = "igk.winui.slider.init({orientation:'{$this->m_orientation}'})";
		return true;
	}
}
