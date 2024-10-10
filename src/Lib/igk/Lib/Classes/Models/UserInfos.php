<?php
// @author: C.A.D. BONDJE DOUE
// @file: UserInfos.php
// @date: 20240922 19:45:49
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property int|\IGK\Models\Users $clUser_Id
* @property int|\IGK\Models\UserInfoTypes $clUserInfoType_Id
* @property string $clValue stored data. not that if data if data length is more than 255 used a table to store that data
* @property string $clDescription description of that value
* @method static string FD_CL_ID() - `clId` full column name 
* @method static string FD_CL_USER_ID() - `clUser_Id` full column name 
* @method static string FD_CL_USER_INFO_TYPE_ID() - `clUserInfoType_Id` full column name 
* @method static string FD_CL_VALUE() - `clValue` full column name 
* @method static string FD_CL_DESCRIPTION() - `clDescription` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(int|\IGK\Models\Users $clUser_Id, int|\IGK\Models\UserInfoTypes $clUserInfoType_Id, string $clValue, string $clDescription) add entry helper
* @method static ?self AddIfNotExists(int|\IGK\Models\Users $clUser_Id, int|\IGK\Models\UserInfoTypes $clUserInfoType_Id, string $clValue, string $clDescription) add entry if not exists. check for unique column.
* */
class UserInfos extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CL_USER_ID="clUser_Id";
	const FD_CL_USER_INFO_TYPE_ID="clUserInfoType_Id";
	const FD_CL_VALUE="clValue";
	const FD_CL_DESCRIPTION="clDescription";
	/**
	* table's name
	*/
	protected $table = "%prefix%user_infos";
}