<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegistrationLinks.php
// @date: 20260102 09:35:11
namespace IGK\Models;
use IGK\Models\ModelBase;
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
* @method static string FN_REG_LINK_ID() - `regLinkId` full column name 
* @method static string FN_REG_LINK_USER_GUID() - `regLinkUserGuid` full column name 
* @method static string FN_REG_LINK_TOKEN() - `regLinkToken` full column name 
* @method static string FN_REG_LINK_ALIVE() - `regLinkAlive` full column name 
* @method static string FN_REG_LINK_ACTIVATE() - `regLinkActivate` full column name 
* @method static string FN_REG_LINK_CREATE_AT() - `regLinkCreate_At` full column name 
* @method static string FN_REG_LINK_UPDATE_AT() - `regLinkUpdate_At` full column name 
* @method static ?array joinOnReglinkid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnReglinkid() - macros function
* @method static ?self Add(string|?\IGK\Models\Users $regLinkUserGuid, string $regLinkToken, int $regLinkAlive, string|datetime $regLinkActivate, string|datetime $regLinkCreate_At ="NOW()", string|datetime $regLinkUpdate_At ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string|?\IGK\Models\Users $regLinkUserGuid, string $regLinkToken, int $regLinkAlive, string|datetime $regLinkActivate, string|datetime $regLinkCreate_At ="NOW()", string|datetime $regLinkUpdate_At ="NOW()") add entry if not exists. check for unique column.
* */
class RegistrationLinks extends ModelBase{
    /**
    * Constant: fd reg link id.
    * @var mixed
    */
    const FD_REG_LINK_ID="regLinkId";
    /**
    * Constant: fd reg link user guid.
    * @var mixed
    */
    const FD_REG_LINK_USER_GUID="regLinkUserGuid";
    /**
    * Constant: fd reg link token.
    * @var mixed
    */
    const FD_REG_LINK_TOKEN="regLinkToken";
    /**
    * Constant: fd reg link alive.
    * @var mixed
    */
    const FD_REG_LINK_ALIVE="regLinkAlive";
    /**
    * Constant: fd reg link activate.
    * @var mixed
    */
    const FD_REG_LINK_ACTIVATE="regLinkActivate";
    /**
    * Constant: fd reg link create at.
    * @var mixed
    */
    const FD_REG_LINK_CREATE_AT="regLinkCreate_At";
    /**
    * Constant: fd reg link update at.
    * @var mixed
    */
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