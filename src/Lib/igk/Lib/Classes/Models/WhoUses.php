<?php
// @author: C.A.D. BONDJE DOUE
// @file: WhoUses.php
// @desc: model file
// @date: 20220802 21:32:00
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property mixed|int $clId
* @property mixed|varchar $clWebSite
* @property mixed|int $clState
* @property mixed|datetime $clDateTime
* @property mixed|varchar $clIP*/
class WhoUses extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%who_uses";
}