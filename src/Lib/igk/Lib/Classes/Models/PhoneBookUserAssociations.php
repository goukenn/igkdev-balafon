<?php
// @author: C.A.D. BONDJE DOUE
// @file: PhoneBookUserAssociations.php
// @date: 20260824 18:08:42
namespace IGK\Models;


use IGK\Models\ModelBase;

/**
* User's Phone books
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $id
* @property string $UserGuid
* @property string|?\IGK\Models\PhoneBookEntries $PhoneBookEntryGuid
* @property string $Create_At ="Now()"
* @property string $Update_At ="Now()"
* @method static string FN_ID() - `id` full column name 
* @method static string FN_USER_GUID() - `UserGuid` full column name 
* @method static string FN_PHONE_BOOK_ENTRY_GUID() - `PhoneBookEntryGuid` full column name 
* @method static string FN_CREATE_AT() - `Create_At` full column name 
* @method static string FN_UPDATE_AT() - `Update_At` full column name 
* @method static ?array joinOnRcphbId($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnRcphbId() - macros function
* @method static ?self Add(string $UserGuid, string|?\IGK\Models\PhoneBookEntries $PhoneBookEntryGuid, string $Create_At ="Now()", string $Update_At ="Now()") add entry helper
* @method static ?self AddIfNotExists(string $UserGuid, string|?\IGK\Models\PhoneBookEntries $PhoneBookEntryGuid, string $Create_At ="Now()", string $Update_At ="Now()") add entry if not exists. check for unique column.
* @method static mixed getEntries() macros function definition
* */
class PhoneBookUserAssociations extends ModelBase{
	const FD_ID="rcphb_id";
	const FD_USER_GUID="rcphb_UserGuid";
	const FD_PHONE_BOOK_ENTRY_GUID="rcphb_PhoneBookEntryGuid";
	const FD_CREATE_AT="rcphb_Create_At";
	const FD_UPDATE_AT="rcphb_Update_At";
	/**
	* table's name
	*/
	protected $table = "%prefix%phoneBookUserAssociations";
	/**
	* override primary key 
	*/
	protected $primaryKey = "rcphb_id";
	/**
	* override refid key 
	*/
	protected $refId = "rcphb_id";
	protected $unique_columns = array (
	  0 => 
	  array (
	    0 => 'rcphb_UserGuid',
	    1 => 'rcphb_PhoneBookEntryGuid',
	  ),
	);
}