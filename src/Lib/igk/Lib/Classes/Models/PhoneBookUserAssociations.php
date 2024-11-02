<?php
// @author: C.A.D. BONDJE DOUE
// @file: PhoneBookUserAssociations.php
// @date: 20240922 19:45:49
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>User's Phone books</summary>
/**
* User's Phone books
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $usrphb_Id
* @property string $usrphb_UserGuid
* @property string|\IGK\Models\PhoneBookEntries $usrphb_PhoneBookEntryGuid
* @property string|datetime $usrphb_Create_At ="Now()"
* @property string|datetime $usrphb_Update_At ="Now()"
* @method static string FD_USRPHB_ID() - `usrphb_Id` full column name 
* @method static string FD_USRPHB_USER_GUID() - `usrphb_UserGuid` full column name 
* @method static string FD_USRPHB_PHONE_BOOK_ENTRY_GUID() - `usrphb_PhoneBookEntryGuid` full column name 
* @method static string FD_USRPHB_CREATE_AT() - `usrphb_Create_At` full column name 
* @method static string FD_USRPHB_UPDATE_AT() - `usrphb_Update_At` full column name 
* @method static ?array joinOnRcphbUsrphbId($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnRcphbUsrphbId() - macros function
* @method static ?self Add(string $usrphb_UserGuid, string|\IGK\Models\PhoneBookEntries $usrphb_PhoneBookEntryGuid, string|datetime $usrphb_Create_At ="Now()", string|datetime $usrphb_Update_At ="Now()") add entry helper
* @method static ?self AddIfNotExists(string $usrphb_UserGuid, string|\IGK\Models\PhoneBookEntries $usrphb_PhoneBookEntryGuid, string|datetime $usrphb_Create_At ="Now()", string|datetime $usrphb_Update_At ="Now()") add entry if not exists. check for unique column.
* @method static void getEntries() macros function
* */
class PhoneBookUserAssociations extends ModelBase{
	const FD_USRPHB_ID="rcphb_usrphb_Id";
	const FD_USRPHB_USER_GUID="rcphb_usrphb_UserGuid";
	const FD_USRPHB_PHONE_BOOK_ENTRY_GUID="rcphb_usrphb_PhoneBookEntryGuid";
	const FD_USRPHB_CREATE_AT="usrphb_Create_At";
	const FD_USRPHB_UPDATE_AT="usrphb_Update_At";
	/**
	* table's name
	*/
	protected $table = "%prefix%phoneBookUserAssociations";
	/**
	* override primary key 
	*/
	protected $primaryKey = "rcphb_usrphb_Id";
	/**
	* override refid key 
	*/
	protected $refId = "rcphb_usrphb_Id";
	protected $unique_columns = array (
	  0 => 
	  array (
	    0 => 'rcphb_usrphb_UserGuid',
	    1 => 'rcphb_usrphb_PhoneBookEntryGuid',
	  ),
	);
}