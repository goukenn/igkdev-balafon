<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbLogs.php
// @date: 20240922 19:45:49
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>store system's log</summary>
/**
* store system's log
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $db_logs_msg
* @property int $db_logs_status
* @property string|datetime $db_logs_createAt ="NOW()"
* @property string $db_logs_tags tags
* @property string|datetime $db_logs_updateAt ="NOW()"
* @method static string FD_CL_ID() - `clId` full column name 
* @method static string FD_DB_LOGS_MSG() - `db_logs_msg` full column name 
* @method static string FD_DB_LOGS_STATUS() - `db_logs_status` full column name 
* @method static string FD_DB_LOGS_CREATE_AT() - `db_logs_createAt` full column name 
* @method static string FD_DB_LOGS_TAGS() - `db_logs_tags` full column name 
* @method static string FD_DB_LOGS_UPDATE_AT() - `db_logs_updateAt` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(string $db_logs_msg, int $db_logs_status, string $db_logs_tags, string|datetime $db_logs_createAt ="NOW()", string|datetime $db_logs_updateAt ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $db_logs_msg, int $db_logs_status, string $db_logs_tags, string|datetime $db_logs_createAt ="NOW()", string|datetime $db_logs_updateAt ="NOW()") add entry if not exists. check for unique column.
* */
class DbLogs extends ModelBase{
	const FD_CL_ID="clId";
	const FD_DB_LOGS_MSG="db_logs_msg";
	const FD_DB_LOGS_STATUS="db_logs_status";
	const FD_DB_LOGS_CREATE_AT="db_logs_createAt";
	const FD_DB_LOGS_TAGS="db_logs_tags";
	const FD_DB_LOGS_UPDATE_AT="db_logs_updateAt";
	/**
	* table's name
	*/
	protected $table = "%prefix%db_logs";
}