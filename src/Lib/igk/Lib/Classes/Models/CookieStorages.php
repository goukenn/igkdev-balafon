<?php
// @author: C.A.D. BONDJE DOUE
// @file: CookieStorages.php
// @desc: model file
// @date: 20220222 03:33:08
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* @package IGK\Models
* @property mixed $clId
* @property mixed $clIdentifier
* @property mixed $clName
* @property mixed $clDateTime
*/
class CookieStorages extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%cookie_storages";
}