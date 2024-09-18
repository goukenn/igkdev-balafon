<?php
// @author: C.A.D. BONDJE DOUE
// @file: UsersReferenceModels.php
// @date: 20240918 08:19:26
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property int|\IGK\Models\Users $clUser_Id
* @property string $clModel
* @property int $clNextValue
* @method static string FD_CL_ID() - `clId` full column name 
* @method static string FD_CL_USER_ID() - `clUser_Id` full column name 
* @method static string FD_CL_MODEL() - `clModel` full column name 
* @method static string FD_CL_NEXT_VALUE() - `clNextValue` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(int|\IGK\Models\Users $clUser_Id, string $clModel, int $clNextValue) add entry helper
* @method static ?self AddIfNotExists(int|\IGK\Models\Users $clUser_Id, string $clModel, int $clNextValue) add entry if not exists. check for unique column.
* */
class UsersReferenceModels extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CL_USER_ID="clUser_Id";
	const FD_CL_MODEL="clModel";
	const FD_CL_NEXT_VALUE="clNextValue";
	/**
	* table's name
	*/
	protected $table = "%prefix%users_reference_models";
}