<?php
// @author: C.A.D. BONDJE DOUE
// @file: Crons.php
// @date: 20230131 13:55:04
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
* @property int $crons_status
* @property string|datetime $crons_create_at ="CURRENT_TIMESTAMP"
* @property string|datetime $crons_update_at ="CURRENT_TIMESTAMP"
* @method static ?self Add(string $crons_name, string $crons_process, string $crons_script, string $crons_options, string $crons_class, int $crons_status, string|datetime $crons_create_at ="CURRENT_TIMESTAMP", string|datetime $crons_update_at ="CURRENT_TIMESTAMP") add entry helper
* @method static ?self AddIfNotExists(string $crons_name, string $crons_process, string $crons_script, string $crons_options, string $crons_class, int $crons_status, string|datetime $crons_create_at ="CURRENT_TIMESTAMP", string|datetime $crons_update_at ="CURRENT_TIMESTAMP") add entry if not exists. check for unique column.
* */
class Crons extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CRONSNAME="crons_name";
	const FD_CRONSPROCESS="crons_process";
	const FD_CRONSSCRIPT="crons_script";
	const FD_CRONSOPTIONS="crons_options";
	const FD_CRONSCLASS="crons_class";
	const FD_CRONSSTATUS="crons_status";
	const FD_CRONSCREATEAT="crons_create_at";
	const FD_CRONSUPDATEAT="crons_update_at";
	/**
	* table's name
	*/
	protected $table = "%prefix%crons";
}