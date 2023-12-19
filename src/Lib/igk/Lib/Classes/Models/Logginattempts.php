<?php
// @author: C.A.D. BONDJE DOUE
// @file: Logginattempts.php
// @date: 20231219 13:50:52
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Store migrations</summary>
/**
* Store migrations
* @package IGK\Models
* @property int $clId
* @property string $logginattempts_login loggin - unique
* @property int $logginattempts_try attemps
* @property string|datetime $logginattempts_createAt ="NOW()" Now
* @property string|datetime $logginattempts_updateAt Last try datetime
* @method static ?self Add(string $logginattempts_login, int $logginattempts_try, string|datetime $logginattempts_updateAt, string|datetime $logginattempts_createAt ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $logginattempts_login, int $logginattempts_try, string|datetime $logginattempts_updateAt, string|datetime $logginattempts_createAt ="NOW()") add entry if not exists. check for unique column.
* */
class Logginattempts extends ModelBase{
	const FD_CL_ID="clId";
	const FD_LOGGINATTEMPTS_LOGIN="logginattempts_login";
	const FD_LOGGINATTEMPTS_TRY="logginattempts_try";
	const FD_LOGGINATTEMPTS_CREATE_AT="logginattempts_createAt";
	const FD_LOGGINATTEMPTS_UPDATE_AT="logginattempts_updateAt";
	/**
	* table's name
	*/
	protected $table = "%prefix%logginattempts";
}