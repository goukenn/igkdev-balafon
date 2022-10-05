<?php
// @author: C.A.D. BONDJE DOUE
// @file: Authorizations.php
// @date: 20220915 17:51:19
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property int $clId
* @property string $clName
* @property string $clController
* @property string $clDescription
* @method static ?self Add(string $clName, string $clController, string $clDescription) add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clController, string $clDescription) add entry if not exists. check for unique column.
* */
class Authorizations extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%authorizations";
}