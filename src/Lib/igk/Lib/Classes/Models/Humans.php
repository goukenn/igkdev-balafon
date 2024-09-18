<?php
// @author: C.A.D. BONDJE DOUE
// @file: Humans.php
// @date: 20240918 08:19:26
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>store human list</summary>
/**
* store human list
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $clGender m or f for male or female
* @property string $clFirstName
* @property string $clLastName
* @method static string FD_CL_ID() - `clId` full column name 
* @method static string FD_CL_GENDER() - `clGender` full column name 
* @method static string FD_CL_FIRST_NAME() - `clFirstName` full column name 
* @method static string FD_CL_LAST_NAME() - `clLastName` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(string $clGender, string $clFirstName, string $clLastName) add entry helper
* @method static ?self AddIfNotExists(string $clGender, string $clFirstName, string $clLastName) add entry if not exists. check for unique column.
* */
class Humans extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CL_GENDER="clGender";
	const FD_CL_FIRST_NAME="clFirstName";
	const FD_CL_LAST_NAME="clLastName";
	/**
	* table's name
	*/
	protected $table = "%prefix%humans";
}