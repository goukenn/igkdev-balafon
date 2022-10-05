<?php
// @author: C.A.D. BONDJE DOUE
// @file: Connexions.php
// @date: 20220915 17:51:19
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary>Store started connexions</summary>
/**
* Store started connexions
* @package IGK\Models
* @property int $clId
* @property int|\IGK\Models\Users $clUser_Id
* @property string|datetime $clDateTime
* @property string $clFrom
* @method static ?self Add(int|\IGK\Models\Users $clUser_Id, string|datetime $clDateTime, string $clFrom) add entry helper
* @method static ?self AddIfNotExists(int|\IGK\Models\Users $clUser_Id, string|datetime $clDateTime, string $clFrom) add entry if not exists. check for unique column.
* */
class Connexions extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%connexions";
}