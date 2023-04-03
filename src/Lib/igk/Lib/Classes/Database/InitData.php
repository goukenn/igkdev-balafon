<?php
// @author: C.A.D. BONDJE DOUE
// @date: 20211106 11:36:51
namespace IGK\Database;
 
use IGK\Controllers\SysDbController as ControllersSysDbController;
use IGK\Models\PhoneBookTypes;
use IGK\System\Constants\PhonebookTypeNames; 
use IGK\System\Database\InitBase;

class InitData extends InitBase{
	public static function Init(ControllersSysDbController $controller){ 

		foreach(PhonebookTypeNames::GetConstants() as $v){
			PhoneBookTypes::insertIfNotExists([
				PhoneBookTypes::FD_RCPHBT_NAME => $v
			]);
		}
	}
}