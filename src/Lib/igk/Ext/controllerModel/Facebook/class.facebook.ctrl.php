<?php

use IGK\Controllers\NonVisibleControllerBase;

class IGKfacebookCtrl extends NonVisibleControllerBase{

	public function getcanAddChild(){
		return false;
	}
	public function initDataEntry($db, $tbname=null){
		$c = igk_getctrl("IGKDataInfoTypesCtrl");
		$n = $c->getDataTableName();
		$db->insert($n, array(IGK_FD_NAME=>"facebooklink"));
	}
}