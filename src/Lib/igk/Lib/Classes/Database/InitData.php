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
/**
* Init data.
* @package IGK\Database
*/
class InitData extends InitBase{
    /**
    * Initializes.
    * @param ControllersSysDbController $controller
    */
    public static function Init(ControllersSysDbController $controller){ 
		// + | init phone books type 
		foreach(PhonebookTypeNames::GetConstants() as $v){
			PhoneBookTypes::insertIfNotExists([
				PhoneBookTypes::FD_NAME => $v,
				PhoneBookTypes::FD_CARDINALITY=>0
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