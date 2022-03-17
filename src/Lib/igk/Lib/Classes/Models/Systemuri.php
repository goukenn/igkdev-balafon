<?php
// @author: C.A.D. BONDJE DOUE
// @file: Systemuri.php
// @desc: model file
// @date: 20220314 11:26:49
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* @package IGK\Models
* @property mixed $clId
* @property mixed $clName
* @property mixed $clUri
*/
class Systemuri extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%systemuri";
}