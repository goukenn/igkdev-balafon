<?php

use IGK\Controllers\NonVisibleControllerBase;

final class IGKCarrouselHtmlItemCtrl extends NonVisibleControllerBase
{
	public function getcanModify(){return false;}
	public function getcanDelete(){return false;}

	public function InitComplete(){
		parent::InitComplete();
		$f =dirname(__FILE__)."/Styles/default.pcss";
        if (file_exists($f))
		    include_once($f);

	}
} 
