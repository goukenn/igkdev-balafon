<?php
// @author: C.A.D. BONDJE DOUE
// @file: PhoneBooks.php
// @date: 20230617 00:34:40
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property int $rcphb_Id
* @property string|\IGK\Models\PhoneBookEntries $rcphb_EntryGuid
* @property int|\IGK\Models\PhoneBookTypes $rcphb_Type
* @property string $rcphb_Value
* @property string|datetime $rcphb_Create_At ="Now()"
* @property string|datetime $rcphb_Update_At ="Now()"
* @method static ?self Add(string|\IGK\Models\PhoneBookEntries $rcphb_EntryGuid, int|\IGK\Models\PhoneBookTypes $rcphb_Type, string $rcphb_Value, string|datetime $rcphb_Create_At ="Now()", string|datetime $rcphb_Update_At ="Now()") add entry helper
* @method static ?self AddIfNotExists(string|\IGK\Models\PhoneBookEntries $rcphb_EntryGuid, int|\IGK\Models\PhoneBookTypes $rcphb_Type, string $rcphb_Value, string|datetime $rcphb_Create_At ="Now()", string|datetime $rcphb_Update_At ="Now()") add entry if not exists. check for unique column.
* */
class PhoneBooks extends ModelBase{
	const FD_RCPHB_ID="rcphb_Id";
	const FD_RCPHB_ENTRY_GUID="rcphb_EntryGuid";
	const FD_RCPHB_TYPE="rcphb_Type";
	const FD_RCPHB_VALUE="rcphb_Value";
	const FD_RCPHB_CREATE_AT="rcphb_Create_At";
	const FD_RCPHB_UPDATE_AT="rcphb_Update_At";
	/**
	* table's name
	*/
	protected $table = "%prefix%phoneBooks"; 
	/**
	* override primary key 
	*/
	protected $primaryKey = "rcphb_Id";
	/**
	* override refid key 
	*/
	protected $refId = "rcphb_Id"; 
	protected $unique_columns = array (
	  0 => 
	  array (
	    0 => 'rcphb_EntryGuid',
	    1 => 'rcphb_Type',
	    2 => 'rcphb_Value',
	  ),
	);
}