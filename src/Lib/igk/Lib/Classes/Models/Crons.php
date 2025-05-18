<?php
// @author: C.A.D. BONDJE DOUE
// @file: Crons.php
// @date: 20250516 07:24:40
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Store cron job</summary>
/**
* Store cron job
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $id
* @property string $name
* @property string $process
* @property string $script
* @property string $options
* @property string $class
* @property int $status running script response status
* @property string|datetime $create_at ="CURRENT_TIMESTAMP"
* @property string|datetime $update_at ="CURRENT_TIMESTAMP"
* @method static string FD_ID() - `id` full column name 
* @method static string FD_NAME() - `name` full column name 
* @method static string FD_PROCESS() - `process` full column name 
* @method static string FD_SCRIPT() - `script` full column name 
* @method static string FD_OPTIONS() - `options` full column name 
* @method static string FD_CLASS() - `class` full column name 
* @method static string FD_STATUS() - `status` full column name 
* @method static string FD_CREATE_AT() - `create_at` full column name 
* @method static string FD_UPDATE_AT() - `update_at` full column name 
* @method static ?array joinOnCronsId($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnCronsId() - macros function
* @method static ?self Add(string $name, string $process, string $script, string $options, string $class, int $status, string|datetime $create_at ="CURRENT_TIMESTAMP", string|datetime $update_at ="CURRENT_TIMESTAMP") add entry helper
* @method static ?self AddIfNotExists(string $name, string $process, string $script, string $options, string $class, int $status, string|datetime $create_at ="CURRENT_TIMESTAMP", string|datetime $update_at ="CURRENT_TIMESTAMP") add entry if not exists. check for unique column.
* */
class Crons extends ModelBase{
	const FD_ID="crons_id";
	const FD_NAME="crons_name";
	const FD_PROCESS="crons_process";
	const FD_SCRIPT="crons_script";
	const FD_OPTIONS="crons_options";
	const FD_CLASS="crons_class";
	const FD_STATUS="crons_status";
	const FD_CREATE_AT="crons_create_at";
	const FD_UPDATE_AT="crons_update_at";
	/**
	* table's name
	*/
	protected $table = "%prefix%crons";
	/**
	* override primary key 
	*/
	protected $primaryKey = "crons_id";
	/**
	* override refid key 
	*/
	protected $refId = "crons_id";
}