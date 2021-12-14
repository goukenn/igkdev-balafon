<?php

use function igk_resources_gets as __;
use IGK\System\Html\Dom\HtmlNode;

class IGKHtmlFormSelectGenderItem extends HtmlNode{
	public function __construct(){
		parent::__construct("select");
		$this->add("option")->setAttribute("value","m")->Content = __("enum.Male");
		$this->add("option")->setAttribute("value","f")->Content = __("enum.Female");
		$this->add("option")->setAttribute("value","a")->Content = __("enum.GenderOther");
		$this->add("option")->setAttribute("value","t")->Content = __("enum.GenderTrans");
	}
} 