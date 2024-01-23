<?php
// @author: C.A.D. BONDJE DOUE
// @file: Connexions.php
// @date: 20240101 17:47:26
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