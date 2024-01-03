<?php
// @author: C.A.D. BONDJE DOUE
// @file: Guids.php
// @date: 20240101 17:47:26
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Store guid on db for living purpose. link to change password for exemple.</summary>
/**
* Store guid on db for living purpose. link to change password for exemple.
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $clGUID
* @property string $clDesc
* @property string|datetime $clCreateAt ="NOW()"
* @method static ?self Add(string $clGUID, string $clDesc, string|datetime $clCreateAt ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $clGUID, string $clDesc, string|datetime $clCreateAt ="NOW()") add entry if not exists. check for unique column.
* */
class Guids extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CL_GUID="clGUID";
	const FD_CL_DESC="clDesc";
	const FD_CL_CREATE_AT="clCreateAt";
	/**
	* table's name
	*/
	protected $table = "%prefix%guids";
}