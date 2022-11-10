<?php

namespace IGK\System\Html\Dom;
use IGK\System\Html\Dom\HtmlNode;

/**
 * canvas zone initial
 * @package 
 */
class CanvaZoneNode extends HtmlNode
{
	private $m_ctrl;

	public function __construct($ctrl){
		parent::__construct("canvas");
		$this->m_ctrl = $ctrl;
		$this["width"] = "320px";
		$this ["height"] = "500px;";
	}
	public function innerHTML(& $xmlOption=null)
	{
		$o = parent::innerHTML($xmlOption);
		$script =  HtmlNode::CreateWebNode("script");
		$script->Content = "window.igk.winui.canva.initctrl('".$this->m_ctrl->getUri("getCanvaRendering")."');";
		$o .= $script->render();
		return $o;
	}
}