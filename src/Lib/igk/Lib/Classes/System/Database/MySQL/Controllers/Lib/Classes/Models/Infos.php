<?php
// @author: C.A.D. BONDJE DOUE
// @file: Infos.php
// @date: 20220915 17:51:19
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property int $clId
* @property string $clName
* @property string $clValue
* @method static ?self Add(string $clName, string $clValue) add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clValue) add entry if not exists. check for unique column.
* */
class Infos extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%infos";
}