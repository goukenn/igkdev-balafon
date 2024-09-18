<?php
// @author: C.A.D. BONDJE DOUE
// @date: 20211106 11:36:51
namespace IGK\Database;
 
use IGK\Controllers\SysDbController as ControllersSysDbController;
use IGK\Models\Configurations;
use IGK\Models\PhoneBookTypes;
use IGK\System\Constants\PhonebookTypeNames; 
use IGK\System\Database\InitBase;
use IGK\System\WinUI\LayoutRules;

class InitData extends InitBase{
	public static function Init(ControllersSysDbController $controller){ 

		foreach(PhonebookTypeNames::GetConstants() as $v){
			PhoneBookTypes::insertIfNotExists([
				PhoneBookTypes::FD_NAME => $v
			]);
		}
		// init layout rules 
		foreach(
			igk_get_class_constants(LayoutRules::class) 
		as $k=>$v){
			Configurations::AddIfNotExists(strtolower('winui.'.$k),$v);
		}
	}
}