<?php
// @author: C.A.D. BONDJE DOUE
// @file: DataTypes.php
// @date: 20220915 17:51:19
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary>Store framework data types</summary>
/**
* Store framework data types
* @package IGK\Models
* @property int $clId
* @property string $clName
* @property string $clDescription
* @property string $clRegex
* @method static ?self Add(string $clName, string $clDescription, string $clRegex) add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clDescription, string $clRegex) add entry if not exists. check for unique column.
* */
class DataTypes extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%data_types";
}