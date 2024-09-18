<?php
// @author: C.A.D. BONDJE DOUE
// @file: Community.php
// @date: 20240918 08:19:26
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $clName identifier of the community
* @property string $clValueType type of data associated to value
* @method static string FD_CL_ID() - `clId` full column name 
* @method static string FD_CL_NAME() - `clName` full column name 
* @method static string FD_CL_VALUE_TYPE() - `clValueType` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(string $clName, string $clValueType) add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clValueType) add entry if not exists. check for unique column.
* */
class Community extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CL_NAME="clName";
	const FD_CL_VALUE_TYPE="clValueType";
	/**
	* table's name
	*/
	protected $table = "%prefix%community";
}