<?php
// @author: C.A.D. BONDJE DOUE
// @file: Users.php
// @date: 20260102 09:35:11
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
* @property string|datetime $clDate ="CURRENT_TIMESTAMP" registration date
* @property string|datetime $clLastLogin last login
* @property int|?\IGK\Models\Users $clParent_Id Parent of this account
* @property string $clClassName if clParent_Id then object refer to class name that initialize the sub user
* @property string|datetime $clcreate_at ="CURRENT_TIMESTAMP" user create at
* @property string|datetime $clupdate_at ="CURRENT_TIMESTAMP" update user's info at
* @property string|datetime $clDeactivate_At user deactivated
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
* @method static string FN_CL_DATE() - `clDate` full column name 
* @method static string FN_CL_LAST_LOGIN() - `clLastLogin` full column name 
* @method static string FN_CL_PARENT_ID() - `clParent_Id` full column name 
* @method static string FN_CL_CLASS_NAME() - `clClassName` full column name 
* @method static string FN_CLCREATE_AT() - `clcreate_at` full column name 
* @method static string FN_CLUPDATE_AT() - `clupdate_at` full column name 
* @method static string FN_CL_DEACTIVATE_AT() - `clDeactivate_At` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(string $clLogin, string $clGuid, string $clPwd, string $clFirstName, string $clLastName, string $clDisplay, string $clPicture, string $clLevel, string|datetime $clLastLogin, int|?\IGK\Models\Users $clParent_Id, string $clClassName, string|datetime $clDeactivate_At, string $clLocale ="fr", int $clStatus ="-1", string|datetime $clDate ="CURRENT_TIMESTAMP", string|datetime $clcreate_at ="CURRENT_TIMESTAMP", string|datetime $clupdate_at ="CURRENT_TIMESTAMP") add entry helper
* @method static ?self AddIfNotExists(string $clLogin, string $clGuid, string $clPwd, string $clFirstName, string $clLastName, string $clDisplay, string $clPicture, string $clLevel, string|datetime $clLastLogin, int|?\IGK\Models\Users $clParent_Id, string $clClassName, string|datetime $clDeactivate_At, string $clLocale ="fr", int $clStatus ="-1", string|datetime $clDate ="CURRENT_TIMESTAMP", string|datetime $clcreate_at ="CURRENT_TIMESTAMP", string|datetime $clupdate_at ="CURRENT_TIMESTAMP") add entry if not exists. check for unique column.
* @method static array CreateUserApiResponseData() macros function
* @method static mixed activate() macros function
* @method static mixed addPhoneBookEntry($type,$value) macros function
* @method static mixed auths() macros function
* @method static mixed bindToGroup(\IGK\Controllers\BaseController $ctrl,string $groupname) macros function
* @method static mixed changePassword(string $newPassword) macros function
* @method static mixed cleanAndDrop() macros function
* @method static mixed fullName() macros function
* @method static mixed getPhoneBookEntries() macros function
* @method static mixed getPhoneBookEntry() macros function
* @method static mixed getPhoneBookEntryByType(?string $type= IGK\System\Constants\PhonebookTypeNames::PHT_PHONE) macros function
* @method static mixed isActive() macros function
* @method static arraybool removeFromGroup(string $groupName) macros function
* @method static mixed resolve($data) macros function
* */
class Users extends ModelBase{
    /**
    * Constant: fd cl id.
    * @var mixed
    */
    const FD_CL_ID="clId";
    /**
    * Constant: fd cl login.
    * @var mixed
    */
    const FD_CL_LOGIN="clLogin";
    /**
    * Constant: fd cl guid.
    * @var mixed
    */
    const FD_CL_GUID="clGuid";
    /**
    * Constant: fd cl pwd.
    * @var mixed
    */
    const FD_CL_PWD="clPwd";
    /**
    * Constant: fd cl first name.
    * @var mixed
    */
    const FD_CL_FIRST_NAME="clFirstName";
    /**
    * Constant: fd cl last name.
    * @var mixed
    */
    const FD_CL_LAST_NAME="clLastName";
    /**
    * Constant: fd cl display.
    * @var mixed
    */
    const FD_CL_DISPLAY="clDisplay";
    /**
    * Constant: fd cl locale.
    * @var mixed
    */
    const FD_CL_LOCALE="clLocale";
    /**
    * Constant: fd cl picture.
    * @var mixed
    */
    const FD_CL_PICTURE="clPicture";
    /**
    * Constant: fd cl level.
    * @var mixed
    */
    const FD_CL_LEVEL="clLevel";
    /**
    * Constant: fd cl status.
    * @var mixed
    */
    const FD_CL_STATUS="clStatus";
    /**
    * Constant: fd cl date.
    * @var mixed
    */
    const FD_CL_DATE="clDate";
    /**
    * Constant: fd cl last login.
    * @var mixed
    */
    const FD_CL_LAST_LOGIN="clLastLogin";
    /**
    * Constant: fd cl parent id.
    * @var mixed
    */
    const FD_CL_PARENT_ID="clParent_Id";
    /**
    * Constant: fd cl class name.
    * @var mixed
    */
    const FD_CL_CLASS_NAME="clClassName";
    /**
    * Constant: fd clcreate at.
    * @var mixed
    */
    const FD_CLCREATE_AT="clcreate_at";
    /**
    * Constant: fd clupdate at.
    * @var mixed
    */
    const FD_CLUPDATE_AT="clupdate_at";
    /**
    * Constant: fd cl deactivate at.
    * @var mixed
    */
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