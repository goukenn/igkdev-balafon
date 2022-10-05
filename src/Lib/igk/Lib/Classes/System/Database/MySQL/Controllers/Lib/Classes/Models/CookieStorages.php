<?php
// @author: C.A.D. BONDJE DOUE
// @file: CookieStorages.php
// @date: 20220915 17:51:19
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
	/**
	* table's name
	*/
	protected $table = "%prefix%cookie_storages";
}