<?php
// @author: C.A.D. BONDJE DOUE
// @file: Crons.php
// @date: 20230922 00:42:27
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
* @property int $crons_status running script response status
* @property string|datetime $crons_create_at ="CURRENT_TIMESTAMP"
* @property string|datetime $crons_update_at ="CURRENT_TIMESTAMP"
* @method static ?self Add(string $crons_name, string $crons_process, string $crons_script, string $crons_options, string $crons_class, int $crons_status, string|datetime $crons_create_at ="CURRENT_TIMESTAMP", string|datetime $crons_update_at ="CURRENT_TIMESTAMP") add entry helper
* @method static ?self AddIfNotExists(string $crons_name, string $crons_process, string $crons_script, string $crons_options, string $crons_class, int $crons_status, string|datetime $crons_create_at ="CURRENT_TIMESTAMP", string|datetime $crons_update_at ="CURRENT_TIMESTAMP") add entry if not exists. check for unique column.
* */
class Crons extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CRONS_NAME="crons_name";
	const FD_CRONS_PROCESS="crons_process";
	const FD_CRONS_SCRIPT="crons_script";
	const FD_CRONS_OPTIONS="crons_options";
	const FD_CRONS_CLASS="crons_class";
	const FD_CRONS_STATUS="crons_status";
	const FD_CRONS_CREATE_AT="crons_create_at";
	const FD_CRONS_UPDATE_AT="crons_update_at";
	/**
	* table's name
	*/
	protected $table = "%prefix%crons";
}