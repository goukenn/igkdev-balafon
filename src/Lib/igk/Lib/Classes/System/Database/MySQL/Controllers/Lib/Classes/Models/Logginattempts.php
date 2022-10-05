<?php
// @author: C.A.D. BONDJE DOUE
// @file: Logginattempts.php
// @date: 20220915 17:51:19
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary>Store migrations</summary>
/**
* Store migrations
* @package IGK\Models
* @property int $clId
* @property string $logginattempts_login
* @property int $logginattempts_try
* @property string|datetime $logginattempts_createAt
* @property string|datetime $logginattempts_updateAt
* @method static ?self Add(string $logginattempts_login, int $logginattempts_try, string|datetime $logginattempts_updateAt, string|datetime $logginattempts_createAt) add entry helper
* @method static ?self AddIfNotExists(string $logginattempts_login, int $logginattempts_try, string|datetime $logginattempts_updateAt, string|datetime $logginattempts_createAt) add entry if not exists. check for unique column.
* */
class Logginattempts extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%logginattempts";
}