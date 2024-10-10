<?php
// @author: C.A.D. BONDJE DOUE
// @file: Backups.php
// @date: 20240922 19:45:48
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
* @method static string FD_CL_ID() - `clId` full column name 
* @method static string FD_BACKUP_TYPE() - `backup_type` full column name 
* @method static string FD_BACKUP_CLASS() - `backup_class` full column name 
* @method static string FD_BACKUP_PATH() - `backup_path` full column name 
* @method static string FD_BACKUP_CREATE_AT() - `backup_create_at` full column name 
* @method static string FD_BACKUP_UPDATE_AT() - `backup_update_at` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
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