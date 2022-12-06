<?php
// @author: C.A.D. BONDJE DOUE
// @file: Groups.php
// @date: 20221203 14:34:18
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
* @property string|datetime $clCreate_at ="NOW()"
* @property string|datetime $clUpdate_at ="NOW()"
* @method static ?self Add(string $clName, string $clDescription, string $clController, string|datetime $clCreate_at ="NOW()", string|datetime $clUpdate_at ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clDescription, string $clController, string|datetime $clCreate_at ="NOW()", string|datetime $clUpdate_at ="NOW()") add entry if not exists. check for unique column.
* */
class Groups extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%groups";
}