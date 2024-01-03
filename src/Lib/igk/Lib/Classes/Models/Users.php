<?php
// @author: C.A.D. BONDJE DOUE
// @file: Users.php
// @date: 20240101 17:47:26
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>User's tables</summary>
/**
* User's tables
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $clLogin
* @property string $clGuid
* @property string $clPwd
* @property string $clFirstName
* @property string $clLastName
* @property string $clDisplay
* @property string $clLocale ="fr"
* @property string $clPicture uri of 255 max length
* @property string $clLevel user's primary level
* @property int $clStatus ="-1" state of the account, -1 = not activated, 1=activated, 0or2=blocked, 4=update
* @property string $google_user_id
* @property string $provider provider name
* @property string $fb_user_id
* @property string|datetime $clDate ="CURRENT_TIMESTAMP" registration date
* @property string|datetime $clLastLogin last login
* @property int|?\IGK\Models\Users $clParent_Id Parent of this account
* @property string $clClassName if clParent_Id then object refer to class name that initialize the sub user
* @property string|datetime $clcreate_at ="CURRENT_TIMESTAMP" user create at
* @property string|datetime $clupdate_at ="CURRENT_TIMESTAMP" update user's info at
* @property string|datetime $clDeactivate_At
* @method static ?self Add(string $clLogin, string $clGuid, string $clPwd, string $clFirstName, string $clLastName, string $clDisplay, string $clPicture, string $clLevel, string $google_user_id, string $provider, string $fb_user_id, string|datetime $clLastLogin, int|?\IGK\Models\Users $clParent_Id, string $clClassName, string|datetime $clDeactivate_At, string $clLocale ="fr", int $clStatus ="-1", string|datetime $clDate ="CURRENT_TIMESTAMP", string|datetime $clcreate_at ="CURRENT_TIMESTAMP", string|datetime $clupdate_at ="CURRENT_TIMESTAMP") add entry helper
* @method static ?self AddIfNotExists(string $clLogin, string $clGuid, string $clPwd, string $clFirstName, string $clLastName, string $clDisplay, string $clPicture, string $clLevel, string $google_user_id, string $provider, string $fb_user_id, string|datetime $clLastLogin, int|?\IGK\Models\Users $clParent_Id, string $clClassName, string|datetime $clDeactivate_At, string $clLocale ="fr", int $clStatus ="-1", string|datetime $clDate ="CURRENT_TIMESTAMP", string|datetime $clcreate_at ="CURRENT_TIMESTAMP", string|datetime $clupdate_at ="CURRENT_TIMESTAMP") add entry if not exists. check for unique column.
* */
class Users extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CL_LOGIN="clLogin";
	const FD_CL_GUID="clGuid";
	const FD_CL_PWD="clPwd";
	const FD_CL_FIRST_NAME="clFirstName";
	const FD_CL_LAST_NAME="clLastName";
	const FD_CL_DISPLAY="clDisplay";
	const FD_CL_LOCALE="clLocale";
	const FD_CL_PICTURE="clPicture";
	const FD_CL_LEVEL="clLevel";
	const FD_CL_STATUS="clStatus";
	const FD_GOOGLE_USER_ID="google_user_id";
	const FD_PROVIDER="provider";
	const FD_FB_USER_ID="fb_user_id";
	const FD_CL_DATE="clDate";
	const FD_CL_LAST_LOGIN="clLastLogin";
	const FD_CL_PARENT_ID="clParent_Id";
	const FD_CL_CLASS_NAME="clClassName";
	const FD_CLCREATE_AT="clcreate_at";
	const FD_CLUPDATE_AT="clupdate_at";
	const FD_CL_DEACTIVATE_AT="clDeactivate_At";
	/**
	* table's name
	*/
	protected $table = "%prefix%users"; 
	/**
	*override hidden key 
	*/
	protected $hidden = ['clPwd'];
}