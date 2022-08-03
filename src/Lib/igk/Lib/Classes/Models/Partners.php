<?php
// @author: C.A.D. BONDJE DOUE
// @file: Partners.php
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
* @property mixed|varchar $clCategory
* @property mixed|text $clWebSite
* @property mixed|text $clDescription*/
class Partners extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%partners";
}