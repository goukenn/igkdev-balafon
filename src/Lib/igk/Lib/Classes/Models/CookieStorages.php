<?php
// @author: C.A.D. BONDJE DOUE
// @file: CookieStorages.php
// @desc: model file
// @date: 20220802 21:32:00
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property mixed|int $clId
* @property mixed|varchar $clIdentifier
* @property mixed|varchar $clName
* @property mixed|datetime $clDateTime*/
class CookieStorages extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%cookie_storages";
}