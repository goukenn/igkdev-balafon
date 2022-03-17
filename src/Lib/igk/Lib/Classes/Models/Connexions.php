<?php
// @author: C.A.D. BONDJE DOUE
// @file: Connexions.php
// @desc: model file
// @date: 20220314 11:26:49
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* @package IGK\Models
* @property mixed $clId
* @property mixed $clUser_Id
* @property mixed $clDateTime
* @property mixed $clFrom
*/
class Connexions extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%connexions";
}