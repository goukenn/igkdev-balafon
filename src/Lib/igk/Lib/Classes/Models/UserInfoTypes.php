<?php
// @author: C.A.D. BONDJE DOUE
// @file: UserInfoTypes.php
// @desc: model file
// @date: 20220802 21:32:00
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property mixed|int $clId
* @property mixed|varchar $clName
* @property mixed|varchar $clDataType
* @property mixed|varchar $clRegex
* @property mixed|int $clCardinality
* @property mixed|int $clType*/
class UserInfoTypes extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%user_info_types";
}