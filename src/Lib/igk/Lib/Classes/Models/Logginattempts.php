<?php
// @author: C.A.D. BONDJE DOUE
// @file: Logginattempts.php
// @date: 20221121 16:32:44
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Store migrations</summary>
/**
* Store migrations
* @package IGK\Models
* @property int $clId
* @property string $logginattempts_login
* @property int $logginattempts_try
* @property string|datetime $logginattempts_createAt ="NOW()"
* @property string|datetime $logginattempts_updateAt
* @method static ?self Add(string $logginattempts_login, int $logginattempts_try, string|datetime $logginattempts_updateAt, string|datetime $logginattempts_createAt ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $logginattempts_login, int $logginattempts_try, string|datetime $logginattempts_updateAt, string|datetime $logginattempts_createAt ="NOW()") add entry if not exists. check for unique column.
* */
class Logginattempts extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%logginattempts";
}