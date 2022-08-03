<?php
// @author: C.A.D. BONDJE DOUE
// @file: UserInfos.php
// @desc: model file
// @date: 20220802 21:32:00
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property mixed|int $clId
* @property mixed|TbigkUsers|int $clUser_Id
* @property mixed|TbigkUserInfoTypes|int $clUserInfoType_Id
* @property mixed|varchar $clValue
* @property mixed|varchar $clDescription*/
class UserInfos extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%user_infos";
}