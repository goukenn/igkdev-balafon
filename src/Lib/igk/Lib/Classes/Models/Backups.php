<?php
// @author: C.A.D. BONDJE DOUE
// @file: Backups.php
// @desc: model file
// @date: 20220705 14:13:39
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property mixed $clId
* @property mixed $backup_type
* @property mixed $backup_class
* @property mixed $backup_path
* @property mixed $backup_create_at
* @property mixed $backup_update_at*/
class Backups extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%backups";
}