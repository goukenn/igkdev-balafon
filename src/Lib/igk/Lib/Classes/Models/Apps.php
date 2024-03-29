<?php
// @author: C.A.D. BONDJE DOUE
// @file: Apps.php
// @date: 20240101 17:47:26
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