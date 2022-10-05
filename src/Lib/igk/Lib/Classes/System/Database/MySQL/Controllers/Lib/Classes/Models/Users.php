<?php
// @author: C.A.D. BONDJE DOUE
// @file: Users.php
// @date: 20220915 17:51:19
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary>User's tables</summary>
/**
* User's tables
* @package IGK\Models
* @property int $clId
* @property string $clLogin
* @property string $clPwd
* @property string $clFirstName
* @property string $clLastName
* @property string $clDisplay
* @property string $clLocale
* @property string $clPicture
* @property string $clLevel
* @property int $clStatus
* @property string|datetime $clDate
* @property string|datetime $clLastLogin
* @property int|?\IGK\Models\Users $clParent_Id
* @property string $clClassName
* @property string|datetime $clcreate_at
* @property string|datetime $clupdate_at
* @property string $clGuid
* @method static ?self Add(string $clLogin, string $clPwd, string $clFirstName, string $clLastName, string $clDisplay, string $clPicture, string $clLevel, string|datetime $clLastLogin, int|?\IGK\Models\Users $clParent_Id, string $clClassName, string $clGuid, string $clLocale, int $clStatus, string|datetime $clDate, string|datetime $clcreate_at, string|datetime $clupdate_at) add entry helper
* @method static ?self AddIfNotExists(string $clLogin, string $clPwd, string $clFirstName, string $clLastName, string $clDisplay, string $clPicture, string $clLevel, string|datetime $clLastLogin, int|?\IGK\Models\Users $clParent_Id, string $clClassName, string $clGuid, string $clLocale, int $clStatus, string|datetime $clDate, string|datetime $clcreate_at, string|datetime $clupdate_at) add entry if not exists. check for unique column.
* */
class Users extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%users";
}