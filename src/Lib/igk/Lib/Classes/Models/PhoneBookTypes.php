<?php
// @author: C.A.D. BONDJE DOUE
// @file: PhoneBookTypes.php
// @date: 20231219 13:50:52
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property int $rcphbt_Id
* @property string $rcphbt_Name
* @property string $rcphbt_Cat
* @property int $rcphbt_Cardinality cardinality of the entry
* @property string|datetime $rcphb_Create_At ="Now()"
* @property string|datetime $rcphb_Update_At ="Now()"
* @method static ?self Add(string $rcphbt_Name, string $rcphbt_Cat, int $rcphbt_Cardinality, string|datetime $rcphb_Create_At ="Now()", string|datetime $rcphb_Update_At ="Now()") add entry helper
* @method static ?self AddIfNotExists(string $rcphbt_Name, string $rcphbt_Cat, int $rcphbt_Cardinality, string|datetime $rcphb_Create_At ="Now()", string|datetime $rcphb_Update_At ="Now()") add entry if not exists. check for unique column.
* */
class PhoneBookTypes extends ModelBase{
	const FD_RCPHBT_ID="rcphbt_Id";
	const FD_RCPHBT_NAME="rcphbt_Name";
	const FD_RCPHBT_CAT="rcphbt_Cat";
	const FD_RCPHBT_CARDINALITY="rcphbt_Cardinality";
	const FD_RCPHB_CREATE_AT="rcphb_Create_At";
	const FD_RCPHB_UPDATE_AT="rcphb_Update_At";
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