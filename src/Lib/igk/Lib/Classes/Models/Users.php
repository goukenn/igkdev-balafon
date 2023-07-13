<?php
// @author: C.A.D. BONDJE DOUE
// @file: Users.php
// @date: 20230705 10:31:06
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
* @property string|datetime $clDate ="CURRENT_TIMESTAMP"
* @property string|datetime $clLastLogin
* @property int|?\IGK\Models\Users $clParent_Id
* @property string $clClassName
* @property string|datetime $clcreate_at ="CURRENT_TIMESTAMP"
* @property string|datetime $clupdate_at ="CURRENT_TIMESTAMP"
* @property string|datetime $clDeactivate_At
* @method static ?self Add(string $clLogin, string $clGuid, string $clPwd, string $clFirstName, string $clLastName, string $clDisplay, string $clPicture, string $clLevel, string|datetime $clLastLogin, int|?\IGK\Models\Users $clParent_Id, string $clClassName, string|datetime $clDeactivate_At, string $clLocale ="fr", int $clStatus ="-1", string|datetime $clDate ="CURRENT_TIMESTAMP", string|datetime $clcreate_at ="CURRENT_TIMESTAMP", string|datetime $clupdate_at ="CURRENT_TIMESTAMP") add entry helper
* @method static ?self AddIfNotExists(string $clLogin, string $clGuid, string $clPwd, string $clFirstName, string $clLastName, string $clDisplay, string $clPicture, string $clLevel, string|datetime $clLastLogin, int|?\IGK\Models\Users $clParent_Id, string $clClassName, string|datetime $clDeactivate_At, string $clLocale ="fr", int $clStatus ="-1", string|datetime $clDate ="CURRENT_TIMESTAMP", string|datetime $clcreate_at ="CURRENT_TIMESTAMP", string|datetime $clupdate_at ="CURRENT_TIMESTAMP") add entry if not exists. check for unique column.
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