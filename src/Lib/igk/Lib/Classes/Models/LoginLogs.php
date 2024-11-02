<?php
// @author: C.A.D. BONDJE DOUE
// @file: LoginLogs.php
// @date: 20240922 19:45:49
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Store connexion history</summary>
/**
* Store connexion history
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $loglogs_Id
* @property string|?\IGK\Models\Users $loglogs_UserGuid
* @property string $loglogs_Agent
* @property string $loglogs_IP
* @property float $loglogs_GeoX location x
* @property float $loglogs_GeoY location y
* @property string $loglogs_Region
* @property string $loglogs_Code
* @property string $loglogs_CountryName
* @property string $loglogs_City
* @property int $loglogs_Status 0 = loggin, 1 = logut
* @property string $loglogs_Description location y
* @property string|datetime $loglogs_Create_At ="NOW()"
* @property string|datetime $loglogs_Update_At ="NOW()"
* @method static string FD_LOGLOGS_ID() - `loglogs_Id` full column name 
* @method static string FD_LOGLOGS_USER_GUID() - `loglogs_UserGuid` full column name 
* @method static string FD_LOGLOGS_AGENT() - `loglogs_Agent` full column name 
* @method static string FD_LOGLOGS_IP() - `loglogs_IP` full column name 
* @method static string FD_LOGLOGS_GEO_X() - `loglogs_GeoX` full column name 
* @method static string FD_LOGLOGS_GEO_Y() - `loglogs_GeoY` full column name 
* @method static string FD_LOGLOGS_REGION() - `loglogs_Region` full column name 
* @method static string FD_LOGLOGS_CODE() - `loglogs_Code` full column name 
* @method static string FD_LOGLOGS_COUNTRY_NAME() - `loglogs_CountryName` full column name 
* @method static string FD_LOGLOGS_CITY() - `loglogs_City` full column name 
* @method static string FD_LOGLOGS_STATUS() - `loglogs_Status` full column name 
* @method static string FD_LOGLOGS_DESCRIPTION() - `loglogs_Description` full column name 
* @method static string FD_LOGLOGS_CREATE_AT() - `loglogs_Create_At` full column name 
* @method static string FD_LOGLOGS_UPDATE_AT() - `loglogs_Update_At` full column name 
* @method static ?array joinOnLoglogsId($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnLoglogsId() - macros function
* @method static ?self Add(string|?\IGK\Models\Users $loglogs_UserGuid, string $loglogs_Agent, string $loglogs_IP, float $loglogs_GeoX, float $loglogs_GeoY, string $loglogs_Region, string $loglogs_Code, string $loglogs_CountryName, string $loglogs_City, int $loglogs_Status, string $loglogs_Description, string|datetime $loglogs_Create_At ="NOW()", string|datetime $loglogs_Update_At ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string|?\IGK\Models\Users $loglogs_UserGuid, string $loglogs_Agent, string $loglogs_IP, float $loglogs_GeoX, float $loglogs_GeoY, string $loglogs_Region, string $loglogs_Code, string $loglogs_CountryName, string $loglogs_City, int $loglogs_Status, string $loglogs_Description, string|datetime $loglogs_Create_At ="NOW()", string|datetime $loglogs_Update_At ="NOW()") add entry if not exists. check for unique column.
* */
class LoginLogs extends ModelBase{
	const FD_LOGLOGS_ID="loglogs_Id";
	const FD_LOGLOGS_USER_GUID="loglogs_UserGuid";
	const FD_LOGLOGS_AGENT="loglogs_Agent";
	const FD_LOGLOGS_IP="loglogs_IP";
	const FD_LOGLOGS_GEO_X="loglogs_GeoX";
	const FD_LOGLOGS_GEO_Y="loglogs_GeoY";
	const FD_LOGLOGS_REGION="loglogs_Region";
	const FD_LOGLOGS_CODE="loglogs_Code";
	const FD_LOGLOGS_COUNTRY_NAME="loglogs_CountryName";
	const FD_LOGLOGS_CITY="loglogs_City";
	const FD_LOGLOGS_STATUS="loglogs_Status";
	const FD_LOGLOGS_DESCRIPTION="loglogs_Description";
	const FD_LOGLOGS_CREATE_AT="loglogs_Create_At";
	const FD_LOGLOGS_UPDATE_AT="loglogs_Update_At";
	/**
	* table's name
	*/
	protected $table = "%prefix%login_logs";
	/**
	* override primary key 
	*/
	protected $primaryKey = "loglogs_Id";
	/**
	* override refid key 
	*/
	protected $refId = "loglogs_Id";
}