<?php
// @author: C.A.D. BONDJE DOUE
// @file: PhoneBookPreferences.php
// @date: 20240922 19:45:49
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>User's preferences</summary>
/**
* User's preferences
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $Id
* @property string|\IGK\Models\PhoneBookEntries $EntryGuid
* @property string|\IGK\Models\PhoneBooks $phoneGuid
* @property string|\IGK\Models\Users $userGuid
* @property int|\IGK\Models\PhoneBookTypes $TypeId
* @property string|datetime $Create_At ="Now()"
* @property string|datetime $Update_At ="Now()"
* @method static string FD_ID() - `Id` full column name 
* @method static string FD_ENTRY_GUID() - `EntryGuid` full column name 
* @method static string FD_PHONE_GUID() - `phoneGuid` full column name 
* @method static string FD_USER_GUID() - `userGuid` full column name 
* @method static string FD_TYPE_ID() - `TypeId` full column name 
* @method static string FD_CREATE_AT() - `Create_At` full column name 
* @method static string FD_UPDATE_AT() - `Update_At` full column name 
* @method static ?array joinOnRcphbpId($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnRcphbpId() - macros function
* @method static ?self Add(string|\IGK\Models\PhoneBookEntries $EntryGuid, string|\IGK\Models\PhoneBooks $phoneGuid, string|\IGK\Models\Users $userGuid, int|\IGK\Models\PhoneBookTypes $TypeId, string|datetime $Create_At ="Now()", string|datetime $Update_At ="Now()") add entry helper
* @method static ?self AddIfNotExists(string|\IGK\Models\PhoneBookEntries $EntryGuid, string|\IGK\Models\PhoneBooks $phoneGuid, string|\IGK\Models\Users $userGuid, int|\IGK\Models\PhoneBookTypes $TypeId, string|datetime $Create_At ="Now()", string|datetime $Update_At ="Now()") add entry if not exists. check for unique column.
* */
class PhoneBookPreferences extends ModelBase{
	const FD_ID="rcphbp_Id";
	const FD_ENTRY_GUID="rcphbp_EntryGuid";
	const FD_PHONE_GUID="rcphbp_phoneGuid";
	const FD_USER_GUID="rcphbp_userGuid";
	const FD_TYPE_ID="rcphbp_TypeId";
	const FD_CREATE_AT="rcphbp_Create_At";
	const FD_UPDATE_AT="rcphbp_Update_At";
	/**
	* table's name
	*/
	protected $table = "%prefix%phoneBookPreferences";
	/**
	* override primary key 
	*/
	protected $primaryKey = "rcphbp_Id";
	/**
	* override refid key 
	*/
	protected $refId = "rcphbp_Id";
	protected $unique_columns = array (
	  0 => 
	  array (
	    0 => 'rcphbp_EntryGuid',
	    1 => 'rcphbp_phoneGuid',
	    2 => 'rcphbp_userGuid',
	    3 => 'rcphbp_TypeId',
	  ),
	);
}