<?php
// @author: C.A.D. BONDJE DOUE
// @file: DataTypes.php
// @date: 20230922 00:42:27
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Store framework data types</summary>
/**
* Store framework data types
* @package IGK\Models
* @property int $clId
* @property string $clName
* @property string $clDescription data description
* @property string $clRegex Regex used to validate data
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