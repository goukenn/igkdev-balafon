<?php
// @author: C.A.D. BONDJE DOUE
// @file: Users.php
// @desc: model file
// @date: 20220802 21:32:00
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property mixed|int $clId
* @property mixed|varchar $clLogin
* @property mixed|varchar $clPwd
* @property mixed|varchar $clFirstName
* @property mixed|varchar $clLastName
* @property mixed|text $clDisplay
* @property mixed|varchar $clLocale
* @property mixed|varchar $clPicture
* @property mixed|enum $clLevel
* @property mixed|int $clStatus
* @property mixed|datetime $clDate
* @property mixed|datetime $clLastLogin
* @property mixed|TbigkUsers|int $clParent_Id
* @property mixed|varchar $clClassName
* @property mixed|datetime $clcreate_at
* @property mixed|datetime $clupdate_at
* @property mixed|varchar $clGuid*/
class Users extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%users";
}