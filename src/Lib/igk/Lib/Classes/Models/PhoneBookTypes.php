<?php
// @author: C.A.D. BONDJE DOUE
// @file: PhoneBookTypes.php
// @date: 20240918 08:19:26
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Phone book's type</summary>
/**
* Phone book's type
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $Id
* @property string $Name
* @property string $Cat
* @property int $Cardinality cardinality of the entry
* @property string|datetime $Create_At ="Now()"
* @property string|datetime $Update_At ="Now()"
* @method static string FD_ID() - `Id` full column name 
* @method static string FD_NAME() - `Name` full column name 
* @method static string FD_CAT() - `Cat` full column name 
* @method static string FD_CARDINALITY() - `Cardinality` full column name 
* @method static string FD_CREATE_AT() - `Create_At` full column name 
* @method static string FD_UPDATE_AT() - `Update_At` full column name 
* @method static ?array joinOnRcphbtId($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnRcphbtId() - macros function
* @method static ?self Add(string $Name, string $Cat, int $Cardinality, string|datetime $Create_At ="Now()", string|datetime $Update_At ="Now()") add entry helper
* @method static ?self AddIfNotExists(string $Name, string $Cat, int $Cardinality, string|datetime $Create_At ="Now()", string|datetime $Update_At ="Now()") add entry if not exists. check for unique column.
* */
class PhoneBookTypes extends ModelBase{
	const FD_ID="rcphbt_Id";
	const FD_NAME="rcphbt_Name";
	const FD_CAT="rcphbt_Cat";
	const FD_CARDINALITY="rcphbt_Cardinality";
	const FD_CREATE_AT="rcphbt_Create_At";
	const FD_UPDATE_AT="rcphbt_Update_At";
	/**
	* table's name
	*/
	protected $table = "%prefix%phoneBookTypes"; 
	/**
	* override primary key 
	*/
	protected $primaryKey = "rcphbt_Id";
	/**
	* override refid key 
	*/
	protected $refId = "rcphbt_Id";
}