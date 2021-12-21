<?php

///<summary>bootraps tool tip item</summary>

use IGK\Controllers\NonVisibleControllerBase;
use IGK\System\Html\Dom\HtmlNode;

final class IGKHtmlBootstrapToolTipItem extends HtmlNode
{
	public function setDataPlacement($v){//direction
		$this["data-placement"] = $v;
		return $this;
	}

	public function getDataPlacement($v){//direction
		return $this["data-placement"];
	}
	public function __construct(){
		parent::__construct("a");
		$this["class"] = "";
		$this["href"] = "#";
		$this["data-toggle"] = "tooltip";
	}

	public function initDemo($t)
	{
		$this->clearChilds();
		$this["title"] = "Demonstration";
		$this->Content = "pass over this ";
		$this->setDataPlacement("right");
		$this->addScript()->Content = <<<EOF
\$igk(igk.getParentScript()).reg_event("mouseover", function(){\$(this).tooltip('show');});
EOF;
	}
}

final class IGKHtmlBootstrapPopoverItem extends HtmlNode
{

	public function setTitle($v){//direction
		$this["data-original-title"] = $v;
		return $this;
	}

	public function getTitle($v){//direction
		return $this["data-original-title"];
	}
	public function setMessage($v){//direction
		$this["data-content"] = $v;
		return $this;
	}

	public function getMessage($v){//direction
		return $this["data-content"];
	}
	public function __construct(){
		parent::__construct("a");
		$this["class"] = "";
		$this["href"] = "#";
		$this["data-toggle"] = "popover";
	}

	public function initDemo($t)
	{
		$this->clearChilds();
		//$this["title"] ="demonstration";
		$this->Content = "pass over this";
		$this["data-original-title"]  = "the title";
		$this["data-content"]  = "this is a content to show. ... ";
		$this->addScript()->Content = <<<EOF
\$igk(igk.getParentScript()).reg_event("mouseover", function(){
	\$(this).popover('show');
}).reg_event('mouseout', function(){
	\$(this).popover('hide');
});
EOF;
	}
}