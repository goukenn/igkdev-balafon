<?php
// @author: C.A.D. BONDJE DOUE
// @file: Users.php
// @date: 20221111 21:30:07
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
* @property string $clLocale ="fr"
* @property string $clPicture
* @property string $clLevel
* @property int $clStatus ="-1"
* @property string|datetime $clDate ="CURRENT_TIMESTAMP"
* @property string|datetime $clLastLogin
* @property int|?\IGK\Models\Users $clParent_Id
* @property string $clClassName
* @property string|datetime $clcreate_at ="CURRENT_TIMESTAMP"
* @property string|datetime $clupdate_at ="CURRENT_TIMESTAMP"
* @property string $clGuid
* @method static ?self Add(string $clLogin, string $clPwd, string $clFirstName, string $clLastName, string $clDisplay, string $clPicture, string $clLevel, string|datetime $clLastLogin, int|?\IGK\Models\Users $clParent_Id, string $clClassName, string $clGuid, string $clLocale ="fr", int $clStatus ="-1", string|datetime $clDate ="CURRENT_TIMESTAMP", string|datetime $clcreate_at ="CURRENT_TIMESTAMP", string|datetime $clupdate_at ="CURRENT_TIMESTAMP") add entry helper
* @method static ?self AddIfNotExists(string $clLogin, string $clPwd, string $clFirstName, string $clLastName, string $clDisplay, string $clPicture, string $clLevel, string|datetime $clLastLogin, int|?\IGK\Models\Users $clParent_Id, string $clClassName, string $clGuid, string $clLocale ="fr", int $clStatus ="-1", string|datetime $clDate ="CURRENT_TIMESTAMP", string|datetime $clcreate_at ="CURRENT_TIMESTAMP", string|datetime $clupdate_at ="CURRENT_TIMESTAMP") add entry if not exists. check for unique column.
* */
class Users extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%users";
}