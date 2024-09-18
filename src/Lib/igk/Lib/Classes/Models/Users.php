<?php
// @author: C.A.D. BONDJE DOUE
// @file: Users.php
// @date: 20240918 08:19:26
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>User's tables</summary>
/**
* User's tables
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $clprofile_ing
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
* @property string|datetime $clDate ="CURRENT_TIMESTAMP" registration date
* @property string|datetime $clLastLogin last login
* @property int|?\IGK\Models\Users $clParent_Id Parent of this account
* @property string $clClassName if clParent_Id then object refer to class name that initialize the sub user
* @property string|datetime $clcreate_at ="CURRENT_TIMESTAMP" user create at
* @property string|datetime $clupdate_at ="CURRENT_TIMESTAMP" update user's info at
* @property string|datetime $clDeactivate_At
* @method static string FD_CL_ID() - `clId` full column name 
* @method static string FD_CLPROFILE_ING() - `clprofile_ing` full column name 
* @method static string FD_CL_LOGIN() - `clLogin` full column name 
* @method static string FD_CL_GUID() - `clGuid` full column name 
* @method static string FD_CL_PWD() - `clPwd` full column name 
* @method static string FD_CL_FIRST_NAME() - `clFirstName` full column name 
* @method static string FD_CL_LAST_NAME() - `clLastName` full column name 
* @method static string FD_CL_DISPLAY() - `clDisplay` full column name 
* @method static string FD_CL_LOCALE() - `clLocale` full column name 
* @method static string FD_CL_PICTURE() - `clPicture` full column name 
* @method static string FD_CL_LEVEL() - `clLevel` full column name 
* @method static string FD_CL_STATUS() - `clStatus` full column name 
* @method static string FD_GOOGLE_USER_ID() - `google_user_id` full column name 
* @method static string FD_PROVIDER() - `provider` full column name 
* @method static string FD_FB_USER_ID() - `fb_user_id` full column name 
* @method static string FD_CL_DATE() - `clDate` full column name 
* @method static string FD_CL_LAST_LOGIN() - `clLastLogin` full column name 
* @method static string FD_CL_PARENT_ID() - `clParent_Id` full column name 
* @method static string FD_CL_CLASS_NAME() - `clClassName` full column name 
* @method static string FD_CLCREATE_AT() - `clcreate_at` full column name 
* @method static string FD_CLUPDATE_AT() - `clupdate_at` full column name 
* @method static string FD_CL_DEACTIVATE_AT() - `clDeactivate_At` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(string $clprofile_ing, string $clLogin, string $clGuid, string $clPwd, string $clFirstName, string $clLastName, string $clDisplay, string $clPicture, string $clLevel, string $google_user_id, string $provider, string $fb_user_id, string|datetime $clLastLogin, int|?\IGK\Models\Users $clParent_Id, string $clClassName, string|datetime $clDeactivate_At, string $clLocale ="fr", int $clStatus ="-1", string|datetime $clDate ="CURRENT_TIMESTAMP", string|datetime $clcreate_at ="CURRENT_TIMESTAMP", string|datetime $clupdate_at ="CURRENT_TIMESTAMP") add entry helper
* @method static ?self AddIfNotExists(string $clprofile_ing, string $clLogin, string $clGuid, string $clPwd, string $clFirstName, string $clLastName, string $clDisplay, string $clPicture, string $clLevel, string $google_user_id, string $provider, string $fb_user_id, string|datetime $clLastLogin, int|?\IGK\Models\Users $clParent_Id, string $clClassName, string|datetime $clDeactivate_At, string $clLocale ="fr", int $clStatus ="-1", string|datetime $clDate ="CURRENT_TIMESTAMP", string|datetime $clcreate_at ="CURRENT_TIMESTAMP", string|datetime $clupdate_at ="CURRENT_TIMESTAMP") add entry if not exists. check for unique column.
* @method static array CreateUserApiResponseData() macros function
* @method static void activate() macros function
* @method static void addPhoneBookEntry($type,$value) macros function
* @method static void auths() macros function
* @method static void bindToGroup(\IGK\Controllers\BaseController $ctrl,string $groupname) macros function
* @method static void changePassword(string $newPassword) macros function
* @method static void fullName() macros function
* @method static void getPhoneBookEntries() macros function
* @method static void getPhoneBookEntry() macros function
* @method static void getPhoneBookEntryByType(?string $type= IGK\System\Constants\PhonebookTypeNames::PHT_PHONE) macros function
* @method static void isActive() macros function
* @method static arraybool removeFromGroup(string $groupName) macros function
* */
class Users extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CLPROFILE_ING="clprofile_ing";
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