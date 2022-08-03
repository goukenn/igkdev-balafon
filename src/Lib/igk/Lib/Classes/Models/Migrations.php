<?php
// @author: C.A.D. BONDJE DOUE
// @file: Migrations.php
// @desc: model file
// @date: 20220802 21:32:00
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property mixed|int $clId
* @property mixed|varchar $migration_name
* @property mixed|int $migration_batch*/
class Migrations extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%migrations";
}