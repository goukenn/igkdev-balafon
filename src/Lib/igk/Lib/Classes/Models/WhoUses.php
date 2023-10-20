<?php
// @author: C.A.D. BONDJE DOUE
// @file: WhoUses.php
// @date: 20230922 00:42:27
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Track who use the framework</summary>
/**
* Track who use the framework
* @package IGK\Models
* @property int $clId
* @property string $clWebSite
* @property int $clState
* @property string|datetime $clDateTime
* @property string $clIP
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