<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbLogs.php
// @desc: model file
// @date: 20220802 21:32:00
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property mixed|int $clId
* @property mixed|text $db_logs_msg
* @property mixed|int $db_logs_status
* @property mixed|datetime $db_logs_createAt
* @property mixed|text $db_logs_tags*/
class DbLogs extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%db_logs";
}