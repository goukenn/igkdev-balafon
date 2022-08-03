<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.facebook.ctrl.php
// @date: 20220803 13:48:59
// @desc: 


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