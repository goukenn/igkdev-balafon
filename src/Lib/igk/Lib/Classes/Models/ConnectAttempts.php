<?php
// @author: C.A.D. BONDJE DOUE
// @file: ConnectAttempts.php
// @date: 20221123 12:07:49
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Store Connexion attempts</summary>
/**
* Store Connexion attempts
* @package IGK\Models
* @property string $cxnId
* @property string $cxnAttempt
* @property string $cxnAccount
* @property int $cxnGeoX
* @property int $cxnGeoY
* @property string|datetime $cxnCreate_at ="NOW()"
* @property string|datetime $cxnUpdate_at ="NOW()"
* @method static ?self Add(string $cxnId, string $cxnAttempt, string $cxnAccount, int $cxnGeoX, int $cxnGeoY, string|datetime $cxnCreate_at ="NOW()", string|datetime $cxnUpdate_at ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $cxnId, string $cxnAttempt, string $cxnAccount, int $cxnGeoX, int $cxnGeoY, string|datetime $cxnCreate_at ="NOW()", string|datetime $cxnUpdate_at ="NOW()") add entry if not exists. check for unique column.
* */
class ConnectAttempts extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%connect_attempts"; 
	/**
	*override primary key 
	*/
	protected $primaryKey = "";
}