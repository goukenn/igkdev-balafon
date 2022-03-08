<?php
// @author: C.A.D. BONDJE DOUE
// @file: Logginattempts.php
// @desc: model file
// @date: 20220222 03:33:09
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* @package IGK\Models
* @property mixed $clId
* @property mixed $logginattempts_login
* @property mixed $logginattempts_try
* @property mixed $logginattempts_createAt
* @property mixed $logginattempts_updateAt
*/
class Logginattempts extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%logginattempts";
}