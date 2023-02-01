<?php
// @author: C.A.D. BONDJE DOUE
// @file: Backups.php
// @date: 20230131 13:55:04
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
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
	const FD_BACKUPTYPE="backup_type";
	const FD_BACKUPCLASS="backup_class";
	const FD_BACKUPPATH="backup_path";
	const FD_BACKUPCREATEAT="backup_create_at";
	const FD_BACKUPUPDATEAT="backup_update_at";
	/**
	* table's name
	*/
	protected $table = "%prefix%backups";
}