<?php
// @author: C.A.D. BONDJE DOUE
// @file: UserInfoTypes.php
// @date: 20240922 19:45:49
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>store use information types.</summary>
/**
* store use information types.
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $clName
* @property string $clDataType data type name
* @property string $clRegex Expression used to valiate data
* @property int $clCardinality cardinality of this value. 0 is infinite or more than 0.
* @property int $clType 1: regex expression to validate the data. 0: database data
* @method static string FD_CL_ID() - `clId` full column name 
* @method static string FD_CL_NAME() - `clName` full column name 
* @method static string FD_CL_DATA_TYPE() - `clDataType` full column name 
* @method static string FD_CL_REGEX() - `clRegex` full column name 
* @method static string FD_CL_CARDINALITY() - `clCardinality` full column name 
* @method static string FD_CL_TYPE() - `clType` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(string $clName, string $clDataType, string $clRegex, int $clCardinality, int $clType) add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clDataType, string $clRegex, int $clCardinality, int $clType) add entry if not exists. check for unique column.
* */
class UserInfoTypes extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CL_NAME="clName";
	const FD_CL_DATA_TYPE="clDataType";
	const FD_CL_REGEX="clRegex";
	const FD_CL_CARDINALITY="clCardinality";
	const FD_CL_TYPE="clType";
	/**
	* table's name
	*/
	protected $table = "%prefix%user_info_types";
}