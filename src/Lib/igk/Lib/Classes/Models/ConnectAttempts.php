<?php
// @author: C.A.D. BONDJE DOUE
// @file: ConnectAttempts.php
// @date: 20230922 00:42:27
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Store Connexion attempts</summary>
/**
* Store Connexion attempts
* @package IGK\Models
* @property string $cxnId provided ip
* @property string $cxnAttempt
* @property string $cxnAccount requested account
* @property int $cxnGeoX location x
* @property int $cxnGeoY location y
* @property string|datetime $cxnCreate_At ="NOW()"
* @property string|datetime $cxnUpdate_At ="NOW()"
* @method static ?self Add(string $cxnId, string $cxnAttempt, string $cxnAccount, int $cxnGeoX, int $cxnGeoY, string|datetime $cxnCreate_At ="NOW()", string|datetime $cxnUpdate_At ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $cxnId, string $cxnAttempt, string $cxnAccount, int $cxnGeoX, int $cxnGeoY, string|datetime $cxnCreate_At ="NOW()", string|datetime $cxnUpdate_At ="NOW()") add entry if not exists. check for unique column.
* */
class ConnectAttempts extends ModelBase{
	const FD_CXN_ID="cxnId";
	const FD_CXN_ATTEMPT="cxnAttempt";
	const FD_CXN_ACCOUNT="cxnAccount";
	const FD_CXN_GEO_X="cxnGeoX";
	const FD_CXN_GEO_Y="cxnGeoY";
	const FD_CXN_CREATE_AT="cxnCreate_At";
	const FD_CXN_UPDATE_AT="cxnUpdate_At";
	/**
	* table's name
	*/
	protected $table = "%prefix%connect_attempts"; 
	/**
	* override primary key 
	*/
	protected $primaryKey = "";
}