<?php
// @author: C.A.D. BONDJE DOUE
// @file: PhoneBookEntries.php
// @date: 20240101 17:47:26
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $rcphbe_Id
* @property string $rcphbe_Guid
* @property string|datetime $rcphbe_Create_At ="Now()"
* @property string|datetime $rcphbe_Update_At ="Now()"
* @method static ?self Add(string $rcphbe_Guid, string|datetime $rcphbe_Create_At ="Now()", string|datetime $rcphbe_Update_At ="Now()") add entry helper
* @method static ?self AddIfNotExists(string $rcphbe_Guid, string|datetime $rcphbe_Create_At ="Now()", string|datetime $rcphbe_Update_At ="Now()") add entry if not exists. check for unique column.
* */
class PhoneBookEntries extends ModelBase{
	const FD_RCPHBE_ID="rcphbe_Id";
	const FD_RCPHBE_GUID="rcphbe_Guid";
	const FD_RCPHBE_CREATE_AT="rcphbe_Create_At";
	const FD_RCPHBE_UPDATE_AT="rcphbe_Update_At";
	/**
	* table's name
	*/
	protected $table = "%prefix%phoneBookEntries"; 
	/**
	* override primary key 
	*/
	protected $primaryKey = "rcphbe_Id";
	/**
	* override refid key 
	*/
	protected $refId = "rcphbe_Id";
}