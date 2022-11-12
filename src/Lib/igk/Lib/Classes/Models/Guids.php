<?php
// @author: C.A.D. BONDJE DOUE
// @file: Guids.php
// @date: 20221111 21:30:07
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Store guid on db for living purpose. link to change password for exemple.</summary>
/**
* Store guid on db for living purpose. link to change password for exemple.
* @package IGK\Models
* @property int $clId
* @property string $clGUID
* @property string $clDesc
* @property string|datetime $clCreateAt ="NOW()"
* @method static ?self Add(string $clGUID, string $clDesc, string|datetime $clCreateAt ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $clGUID, string $clDesc, string|datetime $clCreateAt ="NOW()") add entry if not exists. check for unique column.
* */
class Guids extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%guids";
}