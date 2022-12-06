<?php
// @author: C.A.D. BONDJE DOUE
// @file: Migrations.php
// @date: 20221203 14:34:18
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
	/**
	* table's name
	*/
	protected $table = "%prefix%migrations";
}