<?php
// @author: C.A.D. BONDJE DOUE
// @file: Systemuri.php
// @date: 20220915 17:51:19
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary>store system uri.</summary>
/**
* store system uri.
* @package IGK\Models
* @property int $clId
* @property string $clName
* @property string $clUri
* @method static ?self Add(string $clName, string $clUri) add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clUri) add entry if not exists. check for unique column.
* */
class Systemuri extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%systemuri";
}