<?php
// @author: C.A.D. BONDJE DOUE
// @file: Colors.php
// @date: 20221111 21:30:07
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>use to store named colors.</summary>
/**
* use to store named colors.
* @package IGK\Models
* @property int $clId
* @property string $clName
* @property string $clValue
* @method static ?self Add(string $clName, string $clValue) add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clValue) add entry if not exists. check for unique column.
* */
class Colors extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%colors";
}