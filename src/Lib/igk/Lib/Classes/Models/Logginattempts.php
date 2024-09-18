<?php
// @author: C.A.D. BONDJE DOUE
// @file: Logginattempts.php
// @date: 20240918 08:19:26
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Store migrations</summary>
/**
* Store migrations
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $logginattempts_login loggin - unique
* @property int $logginattempts_try attemps
* @property string|datetime $logginattempts_createAt ="NOW()" Now
* @property string|datetime $logginattempts_updateAt Last try datetime
* @method static string FD_CL_ID() - `clId` full column name 
* @method static string FD_LOGGINATTEMPTS_LOGIN() - `logginattempts_login` full column name 
* @method static string FD_LOGGINATTEMPTS_TRY() - `logginattempts_try` full column name 
* @method static string FD_LOGGINATTEMPTS_CREATE_AT() - `logginattempts_createAt` full column name 
* @method static string FD_LOGGINATTEMPTS_UPDATE_AT() - `logginattempts_updateAt` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
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