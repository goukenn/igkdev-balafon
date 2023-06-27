<?php
// @author: C.A.D. BONDJE DOUE
// @file: Migrations.php
// @date: 20230617 00:34:40
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Store migrations</summary>
/**
* Store migrations
* @package IGK\Models
* @property int $clId
* @property string $migration_name
* @property int $migration_batch
* @property string $migration_desc
* @property string $migration_controller
* @property string|datetime $migration_create_at ="NOW()"
* @property string|datetime $migration_update_at ="NOW()"
* @method static ?self Add(string $migration_name, int $migration_batch, string $migration_desc, string $migration_controller, string|datetime $migration_create_at ="NOW()", string|datetime $migration_update_at ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $migration_name, int $migration_batch, string $migration_desc, string $migration_controller, string|datetime $migration_create_at ="NOW()", string|datetime $migration_update_at ="NOW()") add entry if not exists. check for unique column.
* */
class Migrations extends ModelBase{
	const FD_CL_ID="clId";
	const FD_MIGRATION_NAME="migration_name";
	const FD_MIGRATION_BATCH="migration_batch";
	const FD_MIGRATION_DESC="migration_desc";
	const FD_MIGRATION_CONTROLLER="migration_controller";
	const FD_MIGRATION_CREATE_AT="migration_create_at";
	const FD_MIGRATION_UPDATE_AT="migration_update_at";
	/**
	* table's name
	*/
	protected $table = "%prefix%migrations";
}