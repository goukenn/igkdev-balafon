<?php
// @author: C.A.D. BONDJE DOUE
// @file: Guids.php
// @date: 20260806 19:49:23
namespace IGK\Models;


use IGK\Models\ModelBase;

/**
* Store guid on db for living purpose. link to change password for exemple.
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $clGUID
* @property string $clDesc
* @property string $clCreateAt ="NOW()"
* @method static string FN_CL_ID() - `clId` full column name 
* @method static string FN_CL_GUID() - `clGUID` full column name 
* @method static string FN_CL_DESC() - `clDesc` full column name 
* @method static string FN_CL_CREATE_AT() - `clCreateAt` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(string $clGUID, string $clDesc, string $clCreateAt ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $clGUID, string $clDesc, string $clCreateAt ="NOW()") add entry if not exists. check for unique column.
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