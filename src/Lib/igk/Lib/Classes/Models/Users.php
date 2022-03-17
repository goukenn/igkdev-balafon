<?php
// @author: C.A.D. BONDJE DOUE
// @file: Users.php
// @desc: model file
// @date: 20220314 11:26:49
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* @package IGK\Models
* @property mixed $clId
* @property mixed $clLogin
* @property mixed $clPwd
* @property mixed $clFirstName
* @property mixed $clLastName
* @property mixed $clDisplay
* @property mixed $clLocale
* @property mixed $clPicture
* @property mixed $clLevel
* @property mixed $clStatus
* @property mixed $clDate
* @property mixed $clLastLogin
* @property mixed $clParent_Id
* @property mixed $clClassName
* @property mixed $clcreate_at
* @property mixed $clupdate_at
*/
class Users extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%users";
}