<?php
// @author: C.A.D. BONDJE DOUE
// @file: Groups.php
// @date: 20220915 17:51:19
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary>Store framework groups</summary>
/**
* Store framework groups
* @package IGK\Models
* @property int $clId
* @property string $clName
* @property string $clDescription
* @property string $clController
* @method static ?self Add(string $clName, string $clDescription, string $clController) add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clDescription, string $clController) add entry if not exists. check for unique column.
* */
class Groups extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%groups";
}