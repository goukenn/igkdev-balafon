<?php
// @author: C.A.D. BONDJE DOUE
// @file: WhoUses.php
// @desc: model file
// @date: 20220222 03:33:08
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* @package IGK\Models
* @property mixed $clId
* @property mixed $clWebSite
* @property mixed $clState
* @property mixed $clDateTime
* @property mixed $clIP
*/
class WhoUses extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%who_uses";
}