<?php
// @author: C.A.D. BONDJE DOUE
// @file: Users.php
// @date: 20221119 11:10:14
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>User's tables</summary>
/**
* User's tables
* @package IGK\Models
* @property int $clId
* @property string $clLogin
* @property string $clGuid
* @property string $clPwd
* @property string $clFirstName
* @property string $clLastName
* @property string $clDisplay
* @property string $clLocale ="fr"
* @property string $clPicture
* @property string $clLevel
* @property int $clStatus ="-1"
* @property string $google_user_id
* @property string $provider
* @property string $fb_user_id
* @property string|datetime $clDate ="CURRENT_TIMESTAMP"
* @property string|datetime $clLastLogin
* @property int|?\IGK\Models\Users $clParent_Id
* @property string $clClassName
* @property string|datetime $clcreate_at ="CURRENT_TIMESTAMP"
* @property string|datetime $clupdate_at ="CURRENT_TIMESTAMP"
* @method static ?self Add(string $clLogin, string $clGuid, string $clPwd, string $clFirstName, string $clLastName, string $clDisplay, string $clPicture, string $clLevel, string $google_user_id, string $provider, string $fb_user_id, string|datetime $clLastLogin, int|?\IGK\Models\Users $clParent_Id, string $clClassName, string $clLocale ="fr", int $clStatus ="-1", string|datetime $clDate ="CURRENT_TIMESTAMP", string|datetime $clcreate_at ="CURRENT_TIMESTAMP", string|datetime $clupdate_at ="CURRENT_TIMESTAMP") add entry helper
* @method static ?self AddIfNotExists(string $clLogin, string $clGuid, string $clPwd, string $clFirstName, string $clLastName, string $clDisplay, string $clPicture, string $clLevel, string $google_user_id, string $provider, string $fb_user_id, string|datetime $clLastLogin, int|?\IGK\Models\Users $clParent_Id, string $clClassName, string $clLocale ="fr", int $clStatus ="-1", string|datetime $clDate ="CURRENT_TIMESTAMP", string|datetime $clcreate_at ="CURRENT_TIMESTAMP", string|datetime $clupdate_at ="CURRENT_TIMESTAMP") add entry if not exists. check for unique column.
* */
class Users extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%users";
}