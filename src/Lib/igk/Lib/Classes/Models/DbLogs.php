<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbLogs.php
// @desc: model file
// @date: 20220705 14:13:39
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property mixed $clId
* @property mixed $db_logs_msg
* @property mixed $db_logs_status
* @property mixed $db_logs_createAt
* @property mixed $db_logs_tags*/
class DbLogs extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%db_logs";
}