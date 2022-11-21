<?php
// @author: C.A.D. BONDJE DOUE
// @file: LoginLogs.php
// @date: 20221121 16:32:44
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
* @property string|datetime $regLinkCreate_at ="NOW()"
* @property string|datetime $regLinkUpdate_at ="NOW()"
* @method static ?self Add(string $loglogs_UserGuid, string $loglogs_Agent, string $loglogs_IP, float $loglogs_GeoX, float $loglogs_GeoY, string $loglogs_Region, string $loglogs_Code, string $loglogs_CountryName, string $loglogs_City, int $loglogs_Status, string $loglogs_Description, string|datetime $regLinkCreate_at ="NOW()", string|datetime $regLinkUpdate_at ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $loglogs_UserGuid, string $loglogs_Agent, string $loglogs_IP, float $loglogs_GeoX, float $loglogs_GeoY, string $loglogs_Region, string $loglogs_Code, string $loglogs_CountryName, string $loglogs_City, int $loglogs_Status, string $loglogs_Description, string|datetime $regLinkCreate_at ="NOW()", string|datetime $regLinkUpdate_at ="NOW()") add entry if not exists. check for unique column.
* */
class LoginLogs extends ModelBase{
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