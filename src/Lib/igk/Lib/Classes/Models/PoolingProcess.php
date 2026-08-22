<?php
// @author: C.A.D. BONDJE DOUE
// @file: PoolingProcess.php
// @date: 20260821 14:07:37
namespace IGK\Models;


use IGK\Models\ModelBase;

/**
* Pooling process
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $id
* @property string $name
* @property int $pid
* @property string $ip
* @property string $session_id
* @property string $data
* @property string $description
* @property string $Create_At ="Now()"
* @property string $Update_At ="Now()"
* @method static string FN_ID() - `id` full column name 
* @method static string FN_NAME() - `name` full column name 
* @method static string FN_PID() - `pid` full column name 
* @method static string FN_IP() - `ip` full column name 
* @method static string FN_SESSION_ID() - `session_id` full column name 
* @method static string FN_DATA() - `data` full column name 
* @method static string FN_DESCRIPTION() - `description` full column name 
* @method static string FN_CREATE_AT() - `Create_At` full column name 
* @method static string FN_UPDATE_AT() - `Update_At` full column name 
* @method static ?array joinOnPprocId($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnPprocId() - macros function
* @method static ?self Add(string $name, int $pid, string $ip, string $session_id, string $data, string $description, string $Create_At ="Now()", string $Update_At ="Now()") add entry helper
* @method static ?self AddIfNotExists(string $name, int $pid, string $ip, string $session_id, string $data, string $description, string $Create_At ="Now()", string $Update_At ="Now()") add entry if not exists. check for unique column.
* */
class PoolingProcess extends ModelBase{
	const FD_ID="pproc_id";
	const FD_NAME="pproc_name";
	const FD_PID="pproc_pid";
	const FD_IP="pproc_ip";
	const FD_SESSION_ID="pproc_session_id";
	const FD_DATA="pproc_data";
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