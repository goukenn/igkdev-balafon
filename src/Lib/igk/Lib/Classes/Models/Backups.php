<?php
// @author: C.A.D. BONDJE DOUE
// @file: Backups.php
// @date: 20240101 17:47:26
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $backup_type
* @property string $backup_class
* @property string $backup_path
* @property string|datetime $backup_create_at
* @property string|datetime $backup_update_at
* @method static ?self Add(string $backup_type, string $backup_class, string $backup_path, string|datetime $backup_create_at, string|datetime $backup_update_at) add entry helper
* @method static ?self AddIfNotExists(string $backup_type, string $backup_class, string $backup_path, string|datetime $backup_create_at, string|datetime $backup_update_at) add entry if not exists. check for unique column.
* */
class Backups extends ModelBase{
	const FD_CL_ID="clId";
	const FD_BACKUP_TYPE="backup_type";
	const FD_BACKUP_CLASS="backup_class";
	const FD_BACKUP_PATH="backup_path";
	const FD_BACKUP_CREATE_AT="backup_create_at";
	const FD_BACKUP_UPDATE_AT="backup_update_at";
	/**
	* table's name
	*/
	protected $table = "%prefix%backups";
}