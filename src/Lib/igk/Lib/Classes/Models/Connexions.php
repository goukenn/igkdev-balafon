<?php
// @author: C.A.D. BONDJE DOUE
// @file: Connexions.php
// @date: 20240922 19:45:49
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Store started connexions</summary>
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
* @method static string FD_CL_ID() - `clId` full column name 
* @method static string FD_CL_USER_GUID() - `clUser_Guid` full column name 
* @method static string FD_CL_TOKEN() - `clToken` full column name 
* @method static string FD_CL_TOKEN_INFO() - `clTokenInfo` full column name 
* @method static string FD_CL_DATE_TIME() - `clDateTime` full column name 
* @method static string FD_CL_FROM() - `clFrom` full column name 
* @method static string FD_CNX_CREATE_AT() - `cnx_createAt` full column name 
* @method static string FD_CNX_UPDATE_AT() - `cnx_updateAt` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(string|\IGK\Models\Users $clUser_Guid, string $clToken, string $clTokenInfo, string|datetime $clDateTime, string $clFrom, string|datetime $cnx_updateAt, string|datetime $cnx_createAt ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string|\IGK\Models\Users $clUser_Guid, string $clToken, string $clTokenInfo, string|datetime $clDateTime, string $clFrom, string|datetime $cnx_updateAt, string|datetime $cnx_createAt ="NOW()") add entry if not exists. check for unique column.
* */
class Connexions extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CL_USER_GUID="clUser_Guid";
	const FD_CL_TOKEN="clToken";
	const FD_CL_TOKEN_INFO="clTokenInfo";
	const FD_CL_DATE_TIME="clDateTime";
	const FD_CL_FROM="clFrom";
	const FD_CNX_CREATE_AT="cnx_createAt";
	const FD_CNX_UPDATE_AT="cnx_updateAt";
	/**
	* table's name
	*/
	protected $table = "%prefix%connexions";
}