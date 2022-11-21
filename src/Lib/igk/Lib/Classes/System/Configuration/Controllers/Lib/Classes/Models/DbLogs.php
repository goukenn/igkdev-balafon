<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbLogs.php
// @date: 20221119 11:10:14
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>store system's log</summary>
/**
* store system's log
* @package IGK\Models
* @property int $clId
* @property string $db_logs_msg
* @property int $db_logs_status
* @property string|datetime $db_logs_createAt
* @property string $db_logs_tags
* @method static ?self Add(string $db_logs_msg, int $db_logs_status, string|datetime $db_logs_createAt, string $db_logs_tags) add entry helper
* @method static ?self AddIfNotExists(string $db_logs_msg, int $db_logs_status, string|datetime $db_logs_createAt, string $db_logs_tags) add entry if not exists. check for unique column.
* */
class DbLogs extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%db_logs";
}