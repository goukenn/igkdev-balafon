<?php
// @author: C.A.D. BONDJE DOUE
// @file: DataTypes.php
// @date: 20240922 19:45:48
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Store framework data types</summary>
/**
* Store framework data types
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $clName
* @property string $clDescription data description
* @property string $clRegex Regex used to validate data
* @method static string FD_CL_ID() - `clId` full column name 
* @method static string FD_CL_NAME() - `clName` full column name 
* @method static string FD_CL_DESCRIPTION() - `clDescription` full column name 
* @method static string FD_CL_REGEX() - `clRegex` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(string $clName, string $clDescription, string $clRegex) add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clDescription, string $clRegex) add entry if not exists. check for unique column.
* */
class DataTypes extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CL_NAME="clName";
	const FD_CL_DESCRIPTION="clDescription";
	const FD_CL_REGEX="clRegex";
	/**
	* table's name
	*/
	protected $table = "%prefix%data_types";
}