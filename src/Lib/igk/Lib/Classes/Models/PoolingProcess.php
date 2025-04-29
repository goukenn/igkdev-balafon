<?php
// @author: C.A.D. BONDJE DOUE
// @file: PoolingProcess.php
// @date: 20250428 10:18:55
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Pooling process</summary>
/**
* Pooling process
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $id
* @property string $name
* @property int $pid
* @property string $description
* @property string|datetime $Create_At ="Now()"
* @property string|datetime $Update_At ="Now()"
* @method static string FD_ID() - `id` full column name 
* @method static string FD_NAME() - `name` full column name 
* @method static string FD_PID() - `pid` full column name 
* @method static string FD_DESCRIPTION() - `description` full column name 
* @method static string FD_CREATE_AT() - `Create_At` full column name 
* @method static string FD_UPDATE_AT() - `Update_At` full column name 
* @method static ?array joinOnPprocId($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnPprocId() - macros function
* @method static ?self Add(string $name, int $pid, string $description, string|datetime $Create_At ="Now()", string|datetime $Update_At ="Now()") add entry helper
* @method static ?self AddIfNotExists(string $name, int $pid, string $description, string|datetime $Create_At ="Now()", string|datetime $Update_At ="Now()") add entry if not exists. check for unique column.
* */
class PoolingProcess extends ModelBase{
	const FD_ID="pproc_id";
	const FD_NAME="pproc_name";
	const FD_PID="pproc_pid";
	const FD_DESCRIPTION="pproc_description";
	const FD_CREATE_AT="pproc_Create_At";
	const FD_UPDATE_AT="pproc_Update_At";
	/**
	* table's name
	*/
	protected $table = "%prefix%pooling_process";
	/**
	* override primary key 
	*/
	protected $primaryKey = "pproc_id";
	/**
	* override refid key 
	*/
	protected $refId = "pproc_id";
	/**
	*override display key
	*/
	protected $display = "pproc_name";
}