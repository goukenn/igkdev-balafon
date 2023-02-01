<?php
// @author: C.A.D. BONDJE DOUE
// @file: Logginattempts.php
// @date: 20230131 13:55:04
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Store migrations</summary>
/**
* Store migrations
* @package IGK\Models
* @property int $clId
* @property string $logginattempts_login
* @property int $logginattempts_try
* @property string|datetime $logginattempts_createAt ="NOW()"
* @property string|datetime $logginattempts_updateAt
* @method static ?self Add(string $logginattempts_login, int $logginattempts_try, string|datetime $logginattempts_updateAt, string|datetime $logginattempts_createAt ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $logginattempts_login, int $logginattempts_try, string|datetime $logginattempts_updateAt, string|datetime $logginattempts_createAt ="NOW()") add entry if not exists. check for unique column.
* */
class Logginattempts extends ModelBase{
	const FD_CL_ID="clId";
	const FD_LOGGINATTEMPTSLOGIN="logginattempts_login";
	const FD_LOGGINATTEMPTSTRY="logginattempts_try";
	const FD_LOGGINATTEMPTSCREATE_AT="logginattempts_createAt";
	const FD_LOGGINATTEMPTSUPDATE_AT="logginattempts_updateAt";
	/**
	* table's name
	*/
	protected $table = "%prefix%logginattempts";
}