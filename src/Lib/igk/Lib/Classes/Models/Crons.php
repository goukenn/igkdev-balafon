<?php
// @author: C.A.D. BONDJE DOUE
// @file: Crons.php
// @date: 20260102 09:35:11
namespace IGK\Models;
use IGK\Models\ModelBase;
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
* @method static string FN_ID() - `id` full column name 
* @method static string FN_NAME() - `name` full column name 
* @method static string FN_PROCESS() - `process` full column name 
* @method static string FN_SCRIPT() - `script` full column name 
* @method static string FN_OPTIONS() - `options` full column name 
* @method static string FN_CLASS() - `class` full column name 
* @method static string FN_STATUS() - `status` full column name 
* @method static string FN_CREATE_AT() - `create_at` full column name 
* @method static string FN_UPDATE_AT() - `update_at` full column name 
* @method static ?array joinOnCronsId($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnCronsId() - macros function
* @method static ?self Add(string $name, string $process, string $script, string $options, string $class, int $status, string|datetime $create_at ="CURRENT_TIMESTAMP", string|datetime $update_at ="CURRENT_TIMESTAMP") add entry helper
* @method static ?self AddIfNotExists(string $name, string $process, string $script, string $options, string $class, int $status, string|datetime $create_at ="CURRENT_TIMESTAMP", string|datetime $update_at ="CURRENT_TIMESTAMP") add entry if not exists. check for unique column.
* */
class Crons extends ModelBase{
    /**
    * Constant: fd id.
    * @var mixed
    */
    const FD_ID="crons_id";
    /**
    * Constant: fd name.
    * @var mixed
    */
    const FD_NAME="crons_name";
    /**
    * Constant: fd process.
    * @var mixed
    */
    const FD_PROCESS="crons_process";
    /**
    * Constant: fd script.
    * @var mixed
    */
    const FD_SCRIPT="crons_script";
    /**
    * Constant: fd options.
    * @var mixed
    */
    const FD_OPTIONS="crons_options";
    /**
    * Constant: fd class.
    * @var mixed
    */
    const FD_CLASS="crons_class";
    /**
    * Constant: fd status.
    * @var mixed
    */
    const FD_STATUS="crons_status";
    /**
    * Constant: fd create at.
    * @var mixed
    */
    const FD_CREATE_AT="crons_create_at";
    /**
    * Constant: fd update at.
    * @var mixed
    */
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