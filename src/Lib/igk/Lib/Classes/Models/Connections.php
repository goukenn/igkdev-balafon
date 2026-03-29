<?php
// @author: C.A.D. BONDJE DOUE
// @file: Connections.php
// @date: 20260102 09:35:11
namespace IGK\Models;
use IGK\Models\ModelBase;
/**
* Store started connexions
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string|\IGK\Models\Users $clUser_Guid user request authentication
* @property string $clToken
* @property string $clTokenInfo store token information
* @property string|datetime $clDateTime
* @property string $clFrom
* @property string|datetime $cnx_createAt ="NOW()" Now
* @property string|datetime $cnx_updateAt Last try datetime
* @method static string FN_CL_ID() - `clId` full column name 
* @method static string FN_CL_USER_GUID() - `clUser_Guid` full column name 
* @method static string FN_CL_TOKEN() - `clToken` full column name 
* @method static string FN_CL_TOKEN_INFO() - `clTokenInfo` full column name 
* @method static string FN_CL_DATE_TIME() - `clDateTime` full column name 
* @method static string FN_CL_FROM() - `clFrom` full column name 
* @method static string FN_CNX_CREATE_AT() - `cnx_createAt` full column name 
* @method static string FN_CNX_UPDATE_AT() - `cnx_updateAt` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(string|\IGK\Models\Users $clUser_Id, string $clToken, string $clTokenInfo, string|datetime $clDateTime, string $clFrom, string|datetime $cnx_updateAt, string|datetime $cnx_createAt ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string|\IGK\Models\Users $clUser_Id, string $clToken, string $clTokenInfo, string|datetime $clDateTime, string $clFrom, string|datetime $cnx_updateAt, string|datetime $cnx_createAt ="NOW()") add entry if not exists. check for unique column.
* */
class Connections extends ModelBase{
    /**
    * Constant: fd cl id.
    * @var mixed
    */
    const FD_CL_ID="clId";
    /**
    * Constant: fd cl user guid.
    * @var mixed
    */
    const FD_CL_USER_GUID="clUser_Guid";
    /**
    * Constant: fd cl token.
    * @var mixed
    */
    const FD_CL_TOKEN="clToken";
    /**
    * Constant: fd cl token info.
    * @var mixed
    */
    const FD_CL_TOKEN_INFO="clTokenInfo";
    /**
    * Constant: fd cl date time.
    * @var mixed
    */
    const FD_CL_DATE_TIME="clDateTime";
    /**
    * Constant: fd cl from.
    * @var mixed
    */
    const FD_CL_FROM="clFrom";
    /**
    * Constant: fd cnx create at.
    * @var mixed
    */
    const FD_CNX_CREATE_AT="cnx_createAt";
    /**
    * Constant: fd cnx update at.
    * @var mixed
    */
    const FD_CNX_UPDATE_AT="cnx_updateAt";
	/**
	* table's name
	*/
	protected $table = "%prefix%connections";
	/**
	*override display key
	*/
	protected $display = "clToken";
}