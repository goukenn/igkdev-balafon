<?php
// @author: C.A.D. BONDJE DOUE
// @file: UserInfos.php
// @date: 20221121 16:32:44
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
	/**
	* table's name
	*/
	protected $table = "%prefix%user_infos";
}