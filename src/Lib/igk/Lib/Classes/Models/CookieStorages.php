<?php
// @author: C.A.D. BONDJE DOUE
// @file: CookieStorages.php
// @date: 20230310 13:01:24
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property int $clId
* @property string $clIdentifier
* @property string $clName
* @property string|datetime $clDateTime
* @method static ?self Add(string $clIdentifier, string $clName, string|datetime $clDateTime) add entry helper
* @method static ?self AddIfNotExists(string $clIdentifier, string $clName, string|datetime $clDateTime) add entry if not exists. check for unique column.
* */
class CookieStorages extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CL_IDENTIFIER="clIdentifier";
	const FD_CL_NAME="clName";
	const FD_CL_DATE_TIME="clDateTime";
	/**
	* table's name
	*/
	protected $table = "%prefix%cookie_storages";
}