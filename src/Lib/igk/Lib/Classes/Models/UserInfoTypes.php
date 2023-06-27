<?php
// @author: C.A.D. BONDJE DOUE
// @file: UserInfoTypes.php
// @date: 20230617 00:34:40
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>store use information types.</summary>
/**
* store use information types.
* @package IGK\Models
* @property int $clId
* @property string $clName
* @property string $clDataType
* @property string $clRegex
* @property int $clCardinality
* @property int $clType
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