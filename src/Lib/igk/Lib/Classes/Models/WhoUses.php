<?php
// @author: C.A.D. BONDJE DOUE
// @file: WhoUses.php
// @date: 20240918 08:19:26
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Track who use the framework</summary>
/**
* Track who use the framework
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $clWebSite
* @property int $clState
* @property string|datetime $clDateTime
* @property string $clIP
* @method static string FD_CL_ID() - `clId` full column name 
* @method static string FD_CL_WEB_SITE() - `clWebSite` full column name 
* @method static string FD_CL_STATE() - `clState` full column name 
* @method static string FD_CL_DATE_TIME() - `clDateTime` full column name 
* @method static string FD_CL_IP() - `clIP` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(string $clWebSite, int $clState, string|datetime $clDateTime, string $clIP) add entry helper
* @method static ?self AddIfNotExists(string $clWebSite, int $clState, string|datetime $clDateTime, string $clIP) add entry if not exists. check for unique column.
* */
class WhoUses extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CL_WEB_SITE="clWebSite";
	const FD_CL_STATE="clState";
	const FD_CL_DATE_TIME="clDateTime";
	const FD_CL_IP="clIP";
	/**
	* table's name
	*/
	protected $table = "%prefix%who_uses";
}