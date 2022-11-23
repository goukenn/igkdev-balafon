<?php
// @author: C.A.D. BONDJE DOUE
// @file: Community.php
// @date: 20221123 12:07:49
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property int $clId
* @property string $clName
* @property string $clValueType
* @method static ?self Add(string $clName, string $clValueType) add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clValueType) add entry if not exists. check for unique column.
* */
class Community extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%community";
}