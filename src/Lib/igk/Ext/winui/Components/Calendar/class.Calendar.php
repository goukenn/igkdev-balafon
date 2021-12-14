<?php

//
//Name: calendar controller
//version: 1.0
//

// final class IGKHtmlCalendarItem extends HtmlNode
// {
	// public function __construct(){
		// parent::__construct("div");
	// }
// }

use IGK\Controllers\NonVisibleControllerBase;

final class IGKCalendarHtmlItemCtrl extends NonVisibleControllerBase
{
	public function InitComplete(){
		parent::InitComplete();
        $f =dirname(__FILE__)."/Styles/default.pcss";
        if (file_exists($f))
		    include_once($f);

	}
} 