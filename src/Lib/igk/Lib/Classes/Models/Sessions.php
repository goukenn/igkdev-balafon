<?php
// @author: C.A.D. BONDJE DOUE
// @file: Sessions.php
// @date: 20240922 19:45:49
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Track user started session</summary>
/**
* Track user started session
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $clSessId
* @property string $clSessIp
* @property string|datetime $clSessStartAt ="NOW()"
* @property float $clSessLatitude
* @property float $clSessLongitude
* @property string $clSessCountryName
* @property string $clSessCountryCode
* @property string $clSessCityName
* @property string $clSessRegionName
* @property string $clSessAgent
* @method static string FD_CL_ID() - `clId` full column name 
* @method static string FD_CL_SESS_ID() - `clSessId` full column name 
* @method static string FD_CL_SESS_IP() - `clSessIp` full column name 
* @method static string FD_CL_SESS_START_AT() - `clSessStartAt` full column name 
* @method static string FD_CL_SESS_LATITUDE() - `clSessLatitude` full column name 
* @method static string FD_CL_SESS_LONGITUDE() - `clSessLongitude` full column name 
* @method static string FD_CL_SESS_COUNTRY_NAME() - `clSessCountryName` full column name 
* @method static string FD_CL_SESS_COUNTRY_CODE() - `clSessCountryCode` full column name 
* @method static string FD_CL_SESS_CITY_NAME() - `clSessCityName` full column name 
* @method static string FD_CL_SESS_REGION_NAME() - `clSessRegionName` full column name 
* @method static string FD_CL_SESS_AGENT() - `clSessAgent` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(string $clSessId, string $clSessIp, float $clSessLatitude, float $clSessLongitude, string $clSessCountryName, string $clSessCountryCode, string $clSessCityName, string $clSessRegionName, string $clSessAgent, string|datetime $clSessStartAt ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $clSessId, string $clSessIp, float $clSessLatitude, float $clSessLongitude, string $clSessCountryName, string $clSessCountryCode, string $clSessCityName, string $clSessRegionName, string $clSessAgent, string|datetime $clSessStartAt ="NOW()") add entry if not exists. check for unique column.
* */
class Sessions extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CL_SESS_ID="clSessId";
	const FD_CL_SESS_IP="clSessIp";
	const FD_CL_SESS_START_AT="clSessStartAt";
	const FD_CL_SESS_LATITUDE="clSessLatitude";
	const FD_CL_SESS_LONGITUDE="clSessLongitude";
	const FD_CL_SESS_COUNTRY_NAME="clSessCountryName";
	const FD_CL_SESS_COUNTRY_CODE="clSessCountryCode";
	const FD_CL_SESS_CITY_NAME="clSessCityName";
	const FD_CL_SESS_REGION_NAME="clSessRegionName";
	const FD_CL_SESS_AGENT="clSessAgent";
	/**
	* table's name
	*/
	protected $table = "%prefix%sessions";
}