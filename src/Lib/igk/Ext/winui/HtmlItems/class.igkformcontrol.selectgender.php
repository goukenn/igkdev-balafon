<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.igkformcontrol.selectgender.php
// @date: 20220803 13:48:58
// @desc: 


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