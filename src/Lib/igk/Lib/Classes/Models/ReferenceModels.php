<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReferenceModels.php
// @date: 20240918 08:19:26
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Store reference domain</summary>
/**
* Store reference domain
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $clModel
* @property int $clNextValue
* @method static string FD_CL_ID() - `clId` full column name 
* @method static string FD_CL_MODEL() - `clModel` full column name 
* @method static string FD_CL_NEXT_VALUE() - `clNextValue` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(string $clModel, int $clNextValue) add entry helper
* @method static ?self AddIfNotExists(string $clModel, int $clNextValue) add entry if not exists. check for unique column.
* */
class ReferenceModels extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CL_MODEL="clModel";
	const FD_CL_NEXT_VALUE="clNextValue";
	/**
	* table's name
	*/
	protected $table = "%prefix%reference_models";
}