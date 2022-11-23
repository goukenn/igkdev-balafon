<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbLogs.php
// @date: 20221123 12:07:49
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>store system's log</summary>
/**
* store system's log
* @package IGK\Models
* @property int $clId
* @property string $db_logs_msg
* @property int $db_logs_status
* @property string|datetime $db_logs_createAt ="NOW()"
* @property string $db_logs_tags
* @property string|datetime $db_logs_updateAt ="NOW()"
* @method static ?self Add(string $db_logs_msg, int $db_logs_status, string $db_logs_tags, string|datetime $db_logs_createAt ="NOW()", string|datetime $db_logs_updateAt ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $db_logs_msg, int $db_logs_status, string $db_logs_tags, string|datetime $db_logs_createAt ="NOW()", string|datetime $db_logs_updateAt ="NOW()") add entry if not exists. check for unique column.
* */
class DbLogs extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%db_logs";
}