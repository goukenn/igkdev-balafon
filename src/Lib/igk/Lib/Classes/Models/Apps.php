<?php
// @author: C.A.D. BONDJE DOUE
// @file: Apps.php
// @date: 20221121 16:32:44
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>manage download time application.</summary>
/**
* manage download time application.
* @package IGK\Models
* @property int $clId
* @property string $clName
* @property string $clDownloadTime
* @property string|datetime $clLast
* @method static ?self Add(string $clName, string $clDownloadTime, string|datetime $clLast) add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clDownloadTime, string|datetime $clLast) add entry if not exists. check for unique column.
* */
class Apps extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%apps";
}