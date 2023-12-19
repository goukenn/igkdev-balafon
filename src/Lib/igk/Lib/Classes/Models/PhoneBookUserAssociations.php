<?php
// @author: C.A.D. BONDJE DOUE
// @file: PhoneBookUserAssociations.php
// @date: 20231219 13:50:52
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property int $usrphb_Id
* @property string $usrphb_UserGuid
* @property string|\IGK\Models\PhoneBookEntries $usrphb_PhoneBookEntryGuid
* @property string|datetime $usrphb_Create_At ="Now()"
* @property string|datetime $usrphb_Update_At ="Now()"
* @method static ?self Add(string $usrphb_UserGuid, string|\IGK\Models\PhoneBookEntries $usrphb_PhoneBookEntryGuid, string|datetime $usrphb_Create_At ="Now()", string|datetime $usrphb_Update_At ="Now()") add entry helper
* @method static ?self AddIfNotExists(string $usrphb_UserGuid, string|\IGK\Models\PhoneBookEntries $usrphb_PhoneBookEntryGuid, string|datetime $usrphb_Create_At ="Now()", string|datetime $usrphb_Update_At ="Now()") add entry if not exists. check for unique column.
* */
class PhoneBookUserAssociations extends ModelBase{
	const FD_USRPHB_ID="usrphb_Id";
	const FD_USRPHB_USER_GUID="usrphb_UserGuid";
	const FD_USRPHB_PHONE_BOOK_ENTRY_GUID="usrphb_PhoneBookEntryGuid";
	const FD_USRPHB_CREATE_AT="usrphb_Create_At";
	const FD_USRPHB_UPDATE_AT="usrphb_Update_At";
	/**
	* table's name
	*/
	protected $table = "%prefix%phoneBookUserAssociations"; 
	/**
	* override primary key 
	*/
	protected $primaryKey = "usrphb_Id";
	/**
	* override refid key 
	*/
	protected $refId = "usrphb_Id"; 
	protected $unique_columns = array (
	  0 => 
	  array (
	    0 => 'usrphb_UserGuid',
	    1 => 'usrphb_PhoneBookEntryGuid',
	  ),
	);
}