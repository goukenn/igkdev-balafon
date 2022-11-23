<?php
// @author: C.A.D. BONDJE DOUE
// @file: Configurations.php
// @date: 20221123 12:07:49
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property int $clId
* @property string $clName
* @property string $clValue
* @property string $clDescription
* @method static ?self Add(string $clName, string $clValue, string $clDescription) add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clValue, string $clDescription) add entry if not exists. check for unique column.
* */
class Configurations extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%configurations";
}