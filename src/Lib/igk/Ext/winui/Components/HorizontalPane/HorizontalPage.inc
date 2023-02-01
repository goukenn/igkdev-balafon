<?php

use IGK\System\Html\Dom\HtmlNode;

final class HorizontalPage extends HtmlNode
{
	private $m_options;
	private $m_file; //sorce file

	public function __construct()
	{
		parent::__construct("div");
		$this["class"] = "igk-pane-page";
		$this["igk-control-type"] = "igk-pane-page";
	}
	public function setFile($file)
	{
		$this->m_file = $file;
	}
	public function innerHTML(&$o = null)
	{
		$s = parent::innerHTML($o);
		return $s;
	}
	public function __toString()
	{
		return __CLASS__;
	}
}