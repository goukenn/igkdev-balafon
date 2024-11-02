<?php
// @author: C.A.D. BONDJE DOUE
// @file: PhoneBookEntries.php
// @date: 20240922 19:45:49
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Store book entries</summary>
/**
* Store book entries
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $Id
* @property string $Guid
* @property string|datetime $Create_At ="Now()"
* @property string|datetime $Update_At ="Now()"
* @method static string FD_ID() - `Id` full column name 
* @method static string FD_GUID() - `Guid` full column name 
* @method static string FD_CREATE_AT() - `Create_At` full column name 
* @method static string FD_UPDATE_AT() - `Update_At` full column name 
* @method static ?array joinOnRcphbeId($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnRcphbeId() - macros function
* @method static ?self Add(string $Guid, string|datetime $Create_At ="Now()", string|datetime $Update_At ="Now()") add entry helper
* @method static ?self AddIfNotExists(string $Guid, string|datetime $Create_At ="Now()", string|datetime $Update_At ="Now()") add entry if not exists. check for unique column.
* */
class PhoneBookEntries extends ModelBase{
	const FD_ID="rcphbe_Id";
	const FD_GUID="rcphbe_Guid";
	const FD_CREATE_AT="rcphbe_Create_At";
	const FD_UPDATE_AT="rcphbe_Update_At";
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