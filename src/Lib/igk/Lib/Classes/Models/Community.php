<?php
// @author: C.A.D. BONDJE DOUE
// @file: Community.php
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
* @property mixed|varchar $clValueType*/
class Community extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%community";
}