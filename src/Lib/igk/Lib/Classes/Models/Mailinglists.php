<?php
// @author: C.A.D. BONDJE DOUE
// @file: Mailinglists.php
// @desc: model file
// @date: 20220222 03:33:08
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* @package IGK\Models
* @property mixed $clId
* @property mixed $clEmail
* @property mixed $clState
*/
class Mailinglists extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%mailinglists";
}