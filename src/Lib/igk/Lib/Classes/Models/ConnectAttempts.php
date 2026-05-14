<?php
// @author: C.A.D. BONDJE DOUE
// @file: ConnectAttempts.php
// @date: 20260102 09:35:11
namespace IGK\Models;
use IGK\Models\ModelBase;

/**
* Store connection attempts
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property string $cxnId provided ip
* @property string $cxnAttempt
* @property string $cxnAccount requested account
* @property int $cxnGeoX location x
* @property int $cxnGeoY location y
* @property string $cxnCreate_At ="NOW()"
* @property string $cxnUpdate_At ="NOW()"
* @method static string FN_CXN_ID() - `cxnId` full column name 
* @method static string FN_CXN_ATTEMPT() - `cxnAttempt` full column name 
* @method static string FN_CXN_ACCOUNT() - `cxnAccount` full column name 
* @method static string FN_CXN_GEO_X() - `cxnGeoX` full column name 
* @method static string FN_CXN_GEO_Y() - `cxnGeoY` full column name 
* @method static string FN_CXN_CREATE_AT() - `cxnCreate_At` full column name 
* @method static string FN_CXN_UPDATE_AT() - `cxnUpdate_At` full column name 
* @method static ?array joinOnCxnid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnCxnid() - macros function
* @method static ?self Add(string $cxnId, string $cxnAttempt, string $cxnAccount, int $cxnGeoX, int $cxnGeoY, string|datetime $cxnCreate_At ="NOW()", string|datetime $cxnUpdate_At ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $cxnId, string $cxnAttempt, string $cxnAccount, int $cxnGeoX, int $cxnGeoY, string|datetime $cxnCreate_At ="NOW()", string|datetime $cxnUpdate_At ="NOW()") add entry if not exists. check for unique column.
* */
class ConnectAttempts extends ModelBase{
    /**
    * Constant: fd cxn id.
    * @var mixed
    */
    const FD_CXN_ID="cxnId";
    /**
    * Constant: fd cxn attempt.
    * @var mixed
    */
    const FD_CXN_ATTEMPT="cxnAttempt";
    /**
    * Constant: fd cxn account.
    * @var mixed
    */
    const FD_CXN_ACCOUNT="cxnAccount";
    /**
    * Constant: fd cxn geo x.
    * @var mixed
    */
    const FD_CXN_GEO_X="cxnGeoX";
    /**
    * Constant: fd cxn geo y.
    * @var mixed
    */
    const FD_CXN_GEO_Y="cxnGeoY";
    /**
    * Constant: fd cxn create at.
    * @var mixed
    */
    const FD_CXN_CREATE_AT="cxnCreate_At";
    /**
    * Constant: fd cxn update at.
    * @var mixed
    */
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