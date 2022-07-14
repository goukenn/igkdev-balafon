<?php
// @author: C.A.D. BONDJE DOUE
// @file: Community.php
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
* @property mixed $clValueType*/
class Community extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%community";
}