<?php
// @author: C.A.D. BONDJE DOUE
// @file: Plugins.php
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
* @property mixed|text $clEmail
* @property mixed|datetime $clRelease
* @property mixed|text $clVersion*/
class Plugins extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%plugins";
}