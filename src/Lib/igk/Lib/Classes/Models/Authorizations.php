<?php
// @author: C.A.D. BONDJE DOUE
// @file: Authorizations.php
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
* @property mixed|varchar $clController
* @property mixed|varchar $clDescription*/
class Authorizations extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%authorizations";
}