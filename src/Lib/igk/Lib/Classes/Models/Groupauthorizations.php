<?php
// @author: C.A.D. BONDJE DOUE
// @file: Groupauthorizations.php
// @date: 20240918 08:19:26
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Store framework group authorisation</summary>
/**
* Store framework group authorisation
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property int|\IGK\Models\Groups $clGroup_Id
* @property int|\IGK\Models\Authorizations $clAuth_Id
* @property string $clGrant Grant access depending on the authorization usage
* @property string|datetime $clCreate_At ="NOW()"
* @property string|datetime $clUpdate_At ="NOW()"
* @method static string FD_CL_ID() - `clId` full column name 
* @method static string FD_CL_GROUP_ID() - `clGroup_Id` full column name 
* @method static string FD_CL_AUTH_ID() - `clAuth_Id` full column name 
* @method static string FD_CL_GRANT() - `clGrant` full column name 
* @method static string FD_CL_CREATE_AT() - `clCreate_At` full column name 
* @method static string FD_CL_UPDATE_AT() - `clUpdate_At` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(int|\IGK\Models\Groups $clGroup_Id, int|\IGK\Models\Authorizations $clAuth_Id, string $clGrant, string|datetime $clCreate_At ="NOW()", string|datetime $clUpdate_At ="NOW()") add entry helper
* @method static ?self AddIfNotExists(int|\IGK\Models\Groups $clGroup_Id, int|\IGK\Models\Authorizations $clAuth_Id, string $clGrant, string|datetime $clCreate_At ="NOW()", string|datetime $clUpdate_At ="NOW()") add entry if not exists. check for unique column.
* */
class Groupauthorizations extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CL_GROUP_ID="clGroup_Id";
	const FD_CL_AUTH_ID="clAuth_Id";
	const FD_CL_GRANT="clGrant";
	const FD_CL_CREATE_AT="clCreate_At";
	const FD_CL_UPDATE_AT="clUpdate_At";
	/**
	* table's name
	*/
	protected $table = "%prefix%groupauthorizations";
}