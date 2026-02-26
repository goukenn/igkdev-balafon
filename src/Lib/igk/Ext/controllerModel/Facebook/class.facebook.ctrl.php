<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.facebook.ctrl.php
// @date: 20220803 13:48:59
// @desc: 


use IGK\Controllers\NonVisibleControllerBase;
use IGK\System\Controllers\Traits\NoDbActiveControllerTrait;

/**
* auto generate doc.
*/
class IGKfacebookCtrl extends NonVisibleControllerBase{
	use NoDbActiveControllerTrait;

    /**
    * auto generate doc.
    */
    public function getcanAddChild(){
		return false;
	}

    /**
    * auto generate doc.
    * @param mixed $db
    * @param null|mixed $tbname
    */
    public function initDataEntry($db, $tbname=null){
		$c = igk_getctrl("IGKDataInfoTypesCtrl");
		$n = $c->getDataTableName();
		$db->insert($n, array(IGK_FD_NAME=>"facebooklink"));
	}
}