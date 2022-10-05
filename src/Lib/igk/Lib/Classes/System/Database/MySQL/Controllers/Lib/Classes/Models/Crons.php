<?php
// @author: C.A.D. BONDJE DOUE
// @file: Crons.php
// @date: 20220915 17:51:19
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary>Store cron job</summary>
/**
* Store cron job
* @package IGK\Models
* @property int $clId
* @property string $crons_name
* @property string $crons_process
* @property string $crons_script
* @property string $crons_options
* @property string $crons_class
* @property string|datetime $crons_create_at
* @property string|datetime $crons_update_at
* @method static ?self Add(string $crons_name, string $crons_process, string $crons_script, string $crons_options, string $crons_class, string|datetime $crons_create_at, string|datetime $crons_update_at) add entry helper
* @method static ?self AddIfNotExists(string $crons_name, string $crons_process, string $crons_script, string $crons_options, string $crons_class, string|datetime $crons_create_at, string|datetime $crons_update_at) add entry if not exists. check for unique column.
* */
class Crons extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%crons";
}