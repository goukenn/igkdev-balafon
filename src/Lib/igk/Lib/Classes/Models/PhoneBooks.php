<?php
// @author: C.A.D. BONDJE DOUE
// @file: PhoneBooks.php
// @date: 20240922 19:45:49
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Phone books</summary>
/**
* Phone books
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $Id
* @property string|\IGK\Models\PhoneBookEntries $EntryGuid
* @property int|\IGK\Models\PhoneBookTypes $Type
* @property string $Value
* @property string|datetime $Create_At ="Now()"
* @property string|datetime $Update_At ="Now()"
* @method static string FD_ID() - `Id` full column name 
* @method static string FD_ENTRY_GUID() - `EntryGuid` full column name 
* @method static string FD_TYPE() - `Type` full column name 
* @method static string FD_VALUE() - `Value` full column name 
* @method static string FD_CREATE_AT() - `Create_At` full column name 
* @method static string FD_UPDATE_AT() - `Update_At` full column name 
* @method static ?array joinOnRcphbId($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnRcphbId() - macros function
* @method static ?self Add(string|\IGK\Models\PhoneBookEntries $EntryGuid, int|\IGK\Models\PhoneBookTypes $Type, string $Value, string|datetime $Create_At ="Now()", string|datetime $Update_At ="Now()") add entry helper
* @method static ?self AddIfNotExists(string|\IGK\Models\PhoneBookEntries $EntryGuid, int|\IGK\Models\PhoneBookTypes $Type, string $Value, string|datetime $Create_At ="Now()", string|datetime $Update_At ="Now()") add entry if not exists. check for unique column.
* @method static void GetEntries(?string $entry= null) macros function
* @method static void addPhoneBookEntry(\IGK\Models\Users $user,$value,$type= IGK\System\Constants\PhonebookTypeNames::PHT_PHONE) macros function
* @method static void getPhoneBookEntry(\IGK\Models\Users $user) macros function
* */
class PhoneBooks extends ModelBase{
	const FD_ID="rcphb_Id";
	const FD_ENTRY_GUID="rcphb_EntryGuid";
	const FD_TYPE="rcphb_Type";
	const FD_VALUE="rcphb_Value";
	const FD_CREATE_AT="rcphb_Create_At";
	const FD_UPDATE_AT="rcphb_Update_At";
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