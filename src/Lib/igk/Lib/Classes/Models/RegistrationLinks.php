<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegistrationLinks.php
// @date: 20240922 19:45:49
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>store registration linkss</summary>
/**
* store registration linkss
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $regLinkId
* @property string|?\IGK\Models\Users $regLinkUserGuid
* @property string $regLinkToken token
* @property int $regLinkAlive alive for activation
* @property string|datetime $regLinkActivate activation date
* @property string|datetime $regLinkCreate_At ="NOW()"
* @property string|datetime $regLinkUpdate_At ="NOW()"
* @method static string FD_REG_LINK_ID() - `regLinkId` full column name 
* @method static string FD_REG_LINK_USER_GUID() - `regLinkUserGuid` full column name 
* @method static string FD_REG_LINK_TOKEN() - `regLinkToken` full column name 
* @method static string FD_REG_LINK_ALIVE() - `regLinkAlive` full column name 
* @method static string FD_REG_LINK_ACTIVATE() - `regLinkActivate` full column name 
* @method static string FD_REG_LINK_CREATE_AT() - `regLinkCreate_At` full column name 
* @method static string FD_REG_LINK_UPDATE_AT() - `regLinkUpdate_At` full column name 
* @method static ?array joinOnReglinkid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnReglinkid() - macros function
* @method static ?self Add(string|?\IGK\Models\Users $regLinkUserGuid, string $regLinkToken, int $regLinkAlive, string|datetime $regLinkActivate, string|datetime $regLinkCreate_At ="NOW()", string|datetime $regLinkUpdate_At ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string|?\IGK\Models\Users $regLinkUserGuid, string $regLinkToken, int $regLinkAlive, string|datetime $regLinkActivate, string|datetime $regLinkCreate_At ="NOW()", string|datetime $regLinkUpdate_At ="NOW()") add entry if not exists. check for unique column.
* */
class RegistrationLinks extends ModelBase{
	const FD_REG_LINK_ID="regLinkId";
	const FD_REG_LINK_USER_GUID="regLinkUserGuid";
	const FD_REG_LINK_TOKEN="regLinkToken";
	const FD_REG_LINK_ALIVE="regLinkAlive";
	const FD_REG_LINK_ACTIVATE="regLinkActivate";
	const FD_REG_LINK_CREATE_AT="regLinkCreate_At";
	const FD_REG_LINK_UPDATE_AT="regLinkUpdate_At";
	/**
	* table's name
	*/
	protected $table = "%prefix%registration_links";
	/**
	* override primary key 
	*/
	protected $primaryKey = "regLinkId";
	/**
	* override refid key 
	*/
	protected $refId = "regLinkId";
}