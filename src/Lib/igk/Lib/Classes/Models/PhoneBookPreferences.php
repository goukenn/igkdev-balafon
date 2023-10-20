<?php
// @author: C.A.D. BONDJE DOUE
// @file: PhoneBookPreferences.php
// @date: 20230922 00:42:27
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property int $rcphbp_Id
* @property string|\IGK\Models\PhoneBookEntries $rcphbp_EntryGuid
* @property string|\IGK\Models\PhoneBooks $rcphbp_phoneGuid
* @property string|\IGK\Models\Users $rcphbp_userGuid
* @property int|\IGK\Models\PhoneBookTypes $rcphbp_TypeId
* @property string|datetime $rcphbp_Create_At ="Now()"
* @property string|datetime $rcphbp_Update_At ="Now()"
* @method static ?self Add(string|\IGK\Models\PhoneBookEntries $rcphbp_EntryGuid, string|\IGK\Models\PhoneBooks $rcphbp_phoneGuid, string|\IGK\Models\Users $rcphbp_userGuid, int|\IGK\Models\PhoneBookTypes $rcphbp_TypeId, string|datetime $rcphbp_Create_At ="Now()", string|datetime $rcphbp_Update_At ="Now()") add entry helper
* @method static ?self AddIfNotExists(string|\IGK\Models\PhoneBookEntries $rcphbp_EntryGuid, string|\IGK\Models\PhoneBooks $rcphbp_phoneGuid, string|\IGK\Models\Users $rcphbp_userGuid, int|\IGK\Models\PhoneBookTypes $rcphbp_TypeId, string|datetime $rcphbp_Create_At ="Now()", string|datetime $rcphbp_Update_At ="Now()") add entry if not exists. check for unique column.
* */
class PhoneBookPreferences extends ModelBase{
	const FD_RCPHBP_ID="rcphbp_Id";
	const FD_RCPHBP_ENTRY_GUID="rcphbp_EntryGuid";
	const FD_RCPHBP_PHONE_GUID="rcphbp_phoneGuid";
	const FD_RCPHBP_USER_GUID="rcphbp_userGuid";
	const FD_RCPHBP_TYPE_ID="rcphbp_TypeId";
	const FD_RCPHBP_CREATE_AT="rcphbp_Create_At";
	const FD_RCPHBP_UPDATE_AT="rcphbp_Update_At";
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