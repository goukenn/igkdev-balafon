<?php
// @author: C.A.D. BONDJE DOUE
// @file: LoginLogs.php
// @date: 20230131 13:55:04
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Store connexion history</summary>
/**
* Store connexion history
* @package IGK\Models
* @property int $loglogs_Id
* @property string $loglogs_UserGuid
* @property string $loglogs_Agent
* @property string $loglogs_IP
* @property float $loglogs_GeoX
* @property float $loglogs_GeoY
* @property string $loglogs_Region
* @property string $loglogs_Code
* @property string $loglogs_CountryName
* @property string $loglogs_City
* @property int $loglogs_Status
* @property string $loglogs_Description
* @property string|datetime $regLinkCreate_At ="NOW()"
* @property string|datetime $regLinkUpdate_At ="NOW()"
* @method static ?self Add(string $loglogs_UserGuid, string $loglogs_Agent, string $loglogs_IP, float $loglogs_GeoX, float $loglogs_GeoY, string $loglogs_Region, string $loglogs_Code, string $loglogs_CountryName, string $loglogs_City, int $loglogs_Status, string $loglogs_Description, string|datetime $regLinkCreate_At ="NOW()", string|datetime $regLinkUpdate_At ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $loglogs_UserGuid, string $loglogs_Agent, string $loglogs_IP, float $loglogs_GeoX, float $loglogs_GeoY, string $loglogs_Region, string $loglogs_Code, string $loglogs_CountryName, string $loglogs_City, int $loglogs_Status, string $loglogs_Description, string|datetime $regLinkCreate_At ="NOW()", string|datetime $regLinkUpdate_At ="NOW()") add entry if not exists. check for unique column.
* */
class LoginLogs extends ModelBase{
	const FD_LOGLOGS_ID="loglogs_Id";
	const FD_LOGLOGS_USER_GUID="loglogs_UserGuid";
	const FD_LOGLOGS_AGENT="loglogs_Agent";
	const FD_LOGLOGS_I_P="loglogs_IP";
	const FD_LOGLOGS_GEO_X="loglogs_GeoX";
	const FD_LOGLOGS_GEO_Y="loglogs_GeoY";
	const FD_LOGLOGS_REGION="loglogs_Region";
	const FD_LOGLOGS_CODE="loglogs_Code";
	const FD_LOGLOGS_COUNTRY_NAME="loglogs_CountryName";
	const FD_LOGLOGS_CITY="loglogs_City";
	const FD_LOGLOGS_STATUS="loglogs_Status";
	const FD_LOGLOGS_DESCRIPTION="loglogs_Description";
	const FD_REG_LINK_CREATE_AT="regLinkCreate_At";
	const FD_REG_LINK_UPDATE_AT="regLinkUpdate_At";
	/**
	* table's name
	*/
	protected $table = "%prefix%login_logs"; 
	/**
	*override primary key 
	*/
	protected $primaryKey = "loglogs_Id";
	/**
	*override refid key 
	*/
	protected $refId = "loglogs_Id";
}