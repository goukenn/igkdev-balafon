<?php
// @author: C.A.D. BONDJE DOUE
// @file: Migrations.php
// @desc: model file
// @date: 20220222 03:33:09
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* @package IGK\Models
* @property mixed $clId
* @property mixed $migration_name
* @property mixed $migration_batch
*/
class Migrations extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%migrations";
}