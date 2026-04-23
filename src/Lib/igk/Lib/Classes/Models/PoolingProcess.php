<?php
// @author: C.A.D. BONDJE DOUE
// @file: PoolingProcess.php
// @date: 20260102 09:35:11
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
* @property string|datetime $Create_At ="Now()"
* @property string|datetime $Update_At ="Now()"
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
* @method static ?self Add(string $name, int $pid, string $ip, string $session_id, string $data, string $description, string|datetime $Create_At ="Now()", string|datetime $Update_At ="Now()") add entry helper
* @method static ?self AddIfNotExists(string $name, int $pid, string $ip, string $session_id, string $data, string $description, string|datetime $Create_At ="Now()", string|datetime $Update_At ="Now()") add entry if not exists. check for unique column.
* */
class PoolingProcess extends ModelBase{
    /**
    * Constant: fd id.
    * @var mixed
    */
    const FD_ID="pproc_id";
    /**
    * Constant: fd name.
    * @var mixed
    */
    const FD_NAME="pproc_name";
    /**
    * Constant: fd pid.
    * @var mixed
    */
    const FD_PID="pproc_pid";
    /**
    * Constant: fd ip.
    * @var mixed
    */
    const FD_IP="pproc_ip";
    /**
    * Constant: fd session id.
    * @var mixed
    */
    const FD_SESSION_ID="pproc_session_id";
    /**
    * Constant: fd data.
    * @var mixed
    */
    const FD_DATA="pproc_data";
    /**
    * Constant: fd description.
    * @var mixed
    */
    const FD_DESCRIPTION="pproc_description";
    /**
    * Constant: fd create at.
    * @var mixed
    */
    const FD_CREATE_AT="pproc_Create_At";
    /**
    * Constant: fd update at.
    * @var mixed
    */
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