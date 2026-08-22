<?php
// @author: C.A.D. BONDJE DOUE
// @file: Users.php
// @date: 20260821 14:07:37
namespace IGK\Models;


use IGK\Models\ModelBase;

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
* @property string $clLevel user's primary level|if enum supported error can be truncated
* @property int $clStatus ="-1" state of the account, -1 = not activated, 1=activated, 0or2=blocked, 4=update
* @property string $google_user_id
* @property string $provider provider name
* @property string $fb_user_id
* @property string $auth_2fa_key store otp password key
* @property string $clDate ="CURRENT_TIMESTAMP" registration date
* @property string $clLastLogin last login
* @property int|?\IGK\Models\Users $clParent_Id Parent of this account
* @property string $clClassName if clParent_Id then object refer to class name that initialize the sub user
* @property string $clcreate_at ="CURRENT_TIMESTAMP" user create at
* @property string $clupdate_at ="CURRENT_TIMESTAMP" update user's info at
* @property string $clDeactivate_At user deactivated
* @method static string FN_CL_ID() - `clId` full column name 
* @method static string FN_CL_LOGIN() - `clLogin` full column name 
* @method static string FN_CL_GUID() - `clGuid` full column name 
* @method static string FN_CL_PWD() - `clPwd` full column name 
* @method static string FN_CL_FIRST_NAME() - `clFirstName` full column name 
* @method static string FN_CL_LAST_NAME() - `clLastName` full column name 
* @method static string FN_CL_DISPLAY() - `clDisplay` full column name 
* @method static string FN_CL_LOCALE() - `clLocale` full column name 
* @method static string FN_CL_PICTURE() - `clPicture` full column name 
* @method static string FN_CL_LEVEL() - `clLevel` full column name 
* @method static string FN_CL_STATUS() - `clStatus` full column name 
* @method static string FN_GOOGLE_USER_ID() - `google_user_id` full column name 
* @method static string FN_PROVIDER() - `provider` full column name 
* @method static string FN_FB_USER_ID() - `fb_user_id` full column name 
* @method static string FN_AUTH_FA_KEY() - `auth_2fa_key` full column name 
* @method static string FN_CL_DATE() - `clDate` full column name 
* @method static string FN_CL_LAST_LOGIN() - `clLastLogin` full column name 
* @method static string FN_CL_PARENT_ID() - `clParent_Id` full column name 
* @method static string FN_CL_CLASS_NAME() - `clClassName` full column name 
* @method static string FN_CLCREATE_AT() - `clcreate_at` full column name 
* @method static string FN_CLUPDATE_AT() - `clupdate_at` full column name 
* @method static string FN_CL_DEACTIVATE_AT() - `clDeactivate_At` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(string $clLogin, string $clGuid, string $clPwd, string $clFirstName, string $clLastName, string $clDisplay, string $clPicture, string $clLevel, string $google_user_id, string $provider, string $fb_user_id, string $auth_2fa_key, string $clLastLogin, int|?\IGK\Models\Users $clParent_Id, string $clClassName, string $clDeactivate_At, string $clLocale ="fr", int $clStatus ="-1", string $clDate ="CURRENT_TIMESTAMP", string $clcreate_at ="CURRENT_TIMESTAMP", string $clupdate_at ="CURRENT_TIMESTAMP") add entry helper
* @method static ?self AddIfNotExists(string $clLogin, string $clGuid, string $clPwd, string $clFirstName, string $clLastName, string $clDisplay, string $clPicture, string $clLevel, string $google_user_id, string $provider, string $fb_user_id, string $auth_2fa_key, string $clLastLogin, int|?\IGK\Models\Users $clParent_Id, string $clClassName, string $clDeactivate_At, string $clLocale ="fr", int $clStatus ="-1", string $clDate ="CURRENT_TIMESTAMP", string $clcreate_at ="CURRENT_TIMESTAMP", string $clupdate_at ="CURRENT_TIMESTAMP") add entry if not exists. check for unique column.
* @method static array CreateUserApiResponseData() macros function definition 
* @method static mixed activate() macros function definition 
* @method static mixed addPhoneBookEntry($type,$value) macros function definition 
* @method static mixed auths() macros function definition 
* @method static mixed bindToGroup(\IGK\Controllers\BaseController $ctrl,string $groupname) macros function definition 
* @method static mixed changePassword(string $newPassword) macros function definition 
* @method static mixed cleanAndDrop() macros function definition 
* @method static mixed fullName() macros function definition 
* @method static mixed getPhoneBookEntries() macros function definition 
* @method static mixed getPhoneBookEntry() macros function definition 
* @method static mixed getPhoneBookEntryByType(?string $type= IGK\System\Constants\PhonebookTypeNames::PHT_PHONE) macros function definition 
* @method static mixed isActive() macros function definition 
* @method static bool notRegisterToAProfile(\IGK\Controllers\BaseController $controller) macros function definition 
* @method static bool removeFromGroup(string $groupName) macros function definition 
* @method static mixed resolve($data) macros function definition
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
	const FD_AUTH_FA_KEY="auth_2fa_key";
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
	/**
	*override display key
	*/
	protected $display = "clLogin";
}