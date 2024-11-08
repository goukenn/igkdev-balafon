<?php
// @author: C.A.D. BONDJE DOUE
// @file: Apps.php
// @date: 20240922 19:45:48
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>manage download time application.</summary>
/**
* manage download time application.
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $clName
* @property string $clDownloadTime
* @property string|datetime $clLast Last download time
* @method static string FD_CL_ID() - `clId` full column name 
* @method static string FD_CL_NAME() - `clName` full column name 
* @method static string FD_CL_DOWNLOAD_TIME() - `clDownloadTime` full column name 
* @method static string FD_CL_LAST() - `clLast` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(string $clName, string $clDownloadTime, string|datetime $clLast) add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clDownloadTime, string|datetime $clLast) add entry if not exists. check for unique column.
* */
class Apps extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CL_NAME="clName";
	const FD_CL_DOWNLOAD_TIME="clDownloadTime";
	const FD_CL_LAST="clLast";
	/**
	* table's name
	*/
	protected $table = "%prefix%apps";
}