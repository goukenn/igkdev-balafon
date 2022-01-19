<?php

use IGK\System\Html\Dom\HtmlNode;

final class CalcNode extends HtmlNode
{
	private $m_mode;
	private $m_value;

	public function getMode(){return $this->m_mode; }
	public function setMode($v){$this->m_mode = $v; return $this; }


	public function getValue(){return $this->m_value; }
	public function setValue($v){$this->m_value = $v; return $this; }


	public function __construct(){
		parent::__construct("div");
		$this["class"]="igk-calc";

	}
	public function initView(){
		$this->clearChilds();
		//model de vuew
		$frm = $this->addForm();
		$dv = $frm->div();
		//$dv->addLabel("clValue")->Content = R::ngets("lb.verser");
		$i = $dv->addInput("clValue", "text", new IGKValueListener($this, "Value"))->setAttribute("default-v",new IGKValueListener($this, "Value"));
		$i["class"] = "+alignr";
		$frm->div()->add("span")->Content = "0";
	}
}