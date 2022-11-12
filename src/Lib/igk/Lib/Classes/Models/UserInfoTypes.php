<?php
// @author: C.A.D. BONDJE DOUE
// @file: UserInfoTypes.php
// @date: 20221111 21:30:07
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
	/**
	* table's name
	*/
	protected $table = "%prefix%user_info_types";
}