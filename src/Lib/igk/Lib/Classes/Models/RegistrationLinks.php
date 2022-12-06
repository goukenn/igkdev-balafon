<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegistrationLinks.php
// @date: 20221203 14:34:18
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
* @property string|datetime $regLinkCreate_at ="NOW()"
* @property string|datetime $regLinkUpdate_at ="NOW()"
* @method static ?self Add(string $regLinkUserGuid, string $regLinkToken, int $regLinkAlive, string|datetime $regLinkActivate, string|datetime $regLinkCreate_at ="NOW()", string|datetime $regLinkUpdate_at ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $regLinkUserGuid, string $regLinkToken, int $regLinkAlive, string|datetime $regLinkActivate, string|datetime $regLinkCreate_at ="NOW()", string|datetime $regLinkUpdate_at ="NOW()") add entry if not exists. check for unique column.
* */
class RegistrationLinks extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%registration_links"; 
	/**
	*override primary key 
	*/
	protected $primaryKey = "regLinkId";
	/**
	*override refid key 
	*/
	protected $refId = "regLinkId";
}