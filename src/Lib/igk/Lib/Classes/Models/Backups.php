<?php
// @author: C.A.D. BONDJE DOUE
// @file: Backups.php
// @desc: model file
// @date: 20220802 21:32:00
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property mixed|int $clId
* @property mixed|varchar $backup_type
* @property mixed|text $backup_class
* @property mixed|text $backup_path
* @property mixed|datetime $backup_create_at
* @property mixed|datetime $backup_update_at*/
class Backups extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%backups";
}