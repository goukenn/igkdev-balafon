<?php
// @author: C.A.D. BONDJE DOUE
// @file: Configurations.php
// @desc: model file
// @date: 20220314 11:26:49
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* @package IGK\Models
* @property mixed $clId
* @property mixed $clName
* @property mixed $clValue
* @property mixed $clDescription
*/
class Configurations extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%configurations";
}