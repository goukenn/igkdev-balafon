<?php
// @author: C.A.D. BONDJE DOUE
// @file: PhoneBookPreferredEntries.php
// @date: 20250506 16:08:45
namespace IGK\Models;
use IGK\Models\ModelBase;
/**
* Store book's prefered entries
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $id
* @property string|\IGK\Models\PhoneBookEntries $phone_book_guid
* @property int|\IGK\Models\PhoneBookTypes $type_id
* @property string|datetime $Create_At ="Now()"
* @property string|datetime $Update_At ="Now()"
* @method static string FN_ID() - `id` full column name 
* @method static string FN_PHONE_BOOK_GUID() - `phone_book_guid` full column name 
* @method static string FN_TYPE_ID() - `type_id` full column name 
* @method static string FN_CREATE_AT() - `Create_At` full column name 
* @method static string FN_UPDATE_AT() - `Update_At` full column name 
* @method static ?array joinOnRcphpdId($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnRcphpdId() - macros function
* @method static ?self Add(string|\IGK\Models\PhoneBookEntries $phone_book_guid, int|\IGK\Models\PhoneBookTypes $type_id, string|datetime $Create_At ="Now()", string|datetime $Update_At ="Now()") add entry helper
* @method static ?self AddIfNotExists(string|\IGK\Models\PhoneBookEntries $phone_book_guid, int|\IGK\Models\PhoneBookTypes $type_id, string|datetime $Create_At ="Now()", string|datetime $Update_At ="Now()") add entry if not exists. check for unique column.
* */
class PhoneBookPreferredEntries extends ModelBase{
    /**
    * Constant: fd id.
    * @var mixed
    */
    const FD_ID="rcphpd_id";
    /**
    * Constant: fd phone book guid.
    * @var mixed
    */
    const FD_PHONE_BOOK_GUID="rcphpd_phone_book_guid";
    /**
    * Constant: fd type id.
    * @var mixed
    */
    const FD_TYPE_ID="rcphpd_type_id";
    /**
    * Constant: fd create at.
    * @var mixed
    */
    const FD_CREATE_AT="rcphpd_Create_At";
    /**
    * Constant: fd update at.
    * @var mixed
    */
    const FD_UPDATE_AT="rcphpd_Update_At";
	/**
	* table's name
	*/
	protected $table = "%prefix%phoneBookPreferredEntries";
	/**
	* override primary key 
	*/
	protected $primaryKey = "rcphpd_id";
	/**
	* override refid key 
	*/
	protected $refId = "rcphpd_id";
	/**
	*override display key
	*/
	protected $display = "rcphpd_phone_book_guid";
    /**
    * Property: unique columns.
    * @var mixed
    */
    protected $unique_columns = array (
	  0 => 
	  array (
	    0 => 'rcphpd_phone_book_guid',
	    1 => 'rcphpd_type_id',
	  ),
	);
}