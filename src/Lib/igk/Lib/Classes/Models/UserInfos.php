<?php
// @author: C.A.D. BONDJE DOUE
// @file: UserInfos.php
// @desc: model file
// @date: 20220222 03:33:08
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* @package IGK\Models
* @property mixed $clId
* @property mixed $clUser_Id
* @property mixed $clUserInfoType_Id
* @property mixed $clValue
* @property mixed $clDescription
*/
class UserInfos extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%user_infos";
}