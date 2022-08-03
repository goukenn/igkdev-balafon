<?php
// @author: C.A.D. BONDJE DOUE
// @file: Logginattempts.php
// @desc: model file
// @date: 20220802 21:32:00
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property mixed|int $clId
* @property mixed|varchar $logginattempts_login
* @property mixed|int $logginattempts_try
* @property mixed|datetime $logginattempts_createAt
* @property mixed|datetime $logginattempts_updateAt*/
class Logginattempts extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%logginattempts";
}