<?php
// @author: C.A.D. BONDJE DOUE
// @file: Plugins.php
// @desc: model file
// @date: 20220222 03:33:08
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* @package IGK\Models
* @property mixed $clId
* @property mixed $clName
* @property mixed $clEmail
* @property mixed $clRelease
* @property mixed $clVersion
*/
class Plugins extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%plugins";
}