<?php
// @author: C.A.D. BONDJE DOUE
// @file: ConnectAttempts.php
// @date: 20240922 19:45:49
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Store Connexion attempts</summary>
/**
* Store Connexion attempts
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property string $cxnId provided ip
* @property string $cxnAttempt
* @property string $cxnAccount requested account
* @property int $cxnGeoX location x
* @property int $cxnGeoY location y
* @property string|datetime $cxnCreate_At ="NOW()"
* @property string|datetime $cxnUpdate_At ="NOW()"
* @method static string FD_CXN_ID() - `cxnId` full column name 
* @method static string FD_CXN_ATTEMPT() - `cxnAttempt` full column name 
* @method static string FD_CXN_ACCOUNT() - `cxnAccount` full column name 
* @method static string FD_CXN_GEO_X() - `cxnGeoX` full column name 
* @method static string FD_CXN_GEO_Y() - `cxnGeoY` full column name 
* @method static string FD_CXN_CREATE_AT() - `cxnCreate_At` full column name 
* @method static string FD_CXN_UPDATE_AT() - `cxnUpdate_At` full column name 
* @method static ?array joinOnCxnid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnCxnid() - macros function
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
	protected $primaryKey = "cxnId";
}