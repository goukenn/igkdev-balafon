<?php
// @author: C.A.D. BONDJE DOUE
// @file: Migrations.php
// @date: 20221111 21:30:07
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Store migrations</summary>
/**
* Store migrations
* @package IGK\Models
* @property int $clId
* @property string $migration_name
* @property int $migration_batch
* @method static ?self Add(string $migration_name, int $migration_batch) add entry helper
* @method static ?self AddIfNotExists(string $migration_name, int $migration_batch) add entry if not exists. check for unique column.
* */
class Migrations extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%migrations";
}