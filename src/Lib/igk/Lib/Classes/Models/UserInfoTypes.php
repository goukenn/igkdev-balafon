<?php
// @author: C.A.D. BONDJE DOUE
// @file: UserInfoTypes.php
// @desc: model file
// @date: 20220705 14:13:39
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property mixed $clId
* @property mixed $clName
* @property mixed $clDataType
* @property mixed $clRegex
* @property mixed $clCardinality
* @property mixed $clType*/
class UserInfoTypes extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%user_info_types";
}