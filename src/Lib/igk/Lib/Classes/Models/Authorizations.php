<?php
// @author: C.A.D. BONDJE DOUE
// @file: Authorizations.php
// @date: 20221203 14:34:18
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
* @property string|datetime $clCreate_at ="NOW()"
* @property string|datetime $clUpdate_at ="NOW()"
* @method static ?self Add(string $clName, string $clController, string $clDescription, string|datetime $clCreate_at ="NOW()", string|datetime $clUpdate_at ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clController, string $clDescription, string|datetime $clCreate_at ="NOW()", string|datetime $clUpdate_at ="NOW()") add entry if not exists. check for unique column.
* */
class Authorizations extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%authorizations";
}