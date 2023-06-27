<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegistrationLinks.php
// @date: 20230617 00:34:40
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>store registration linkss</summary>
/**
* store registration linkss
* @package IGK\Models
* @property int $regLinkId
* @property string $regLinkUserGuid
* @property string $regLinkToken
* @property int $regLinkAlive
* @property string|datetime $regLinkActivate
* @property string|datetime $regLinkCreate_At ="NOW()"
* @property string|datetime $regLinkUpdate_At ="NOW()"
* @method static ?self Add(string $regLinkUserGuid, string $regLinkToken, int $regLinkAlive, string|datetime $regLinkActivate, string|datetime $regLinkCreate_At ="NOW()", string|datetime $regLinkUpdate_At ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $regLinkUserGuid, string $regLinkToken, int $regLinkAlive, string|datetime $regLinkActivate, string|datetime $regLinkCreate_At ="NOW()", string|datetime $regLinkUpdate_At ="NOW()") add entry if not exists. check for unique column.
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