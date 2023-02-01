<?php
// @author: C.A.D. BONDJE DOUE
// @file: UserInfos.php
// @date: 20230131 13:55:04
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property int $clId
* @property int|\IGK\Models\Users $clUser_Id
* @property int|\IGK\Models\UserInfoTypes $clUserInfoType_Id
* @property string $clValue
* @property string $clDescription
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