<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbLogs.php
// @desc: model file
// @date: 20220123 10:37:53
namespace IGK\Models;

use IGK\Models\ModelBase;

class DbLogs extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%db_logs";
}