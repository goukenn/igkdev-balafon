<?php
// @author: C.A.D. BONDJE DOUE
// @file: Sessions.php
// @date: 20260102 09:35:11
namespace IGK\Models;
use IGK\Models\ModelBase;

/**
* Track user started session
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $clSessId
* @property string $clSessIp
* @property string $clSessStartAt ="NOW()"
* @property float $clSessLatitude
* @property float $clSessLongitude
* @property string $clSessCountryName
* @property string $clSessCountryCode
* @property string $clSessCityName
* @property string $clSessRegionName
* @property string $clSessAgent
* @method static string FN_CL_ID() - `clId` full column name 
* @method static string FN_CL_SESS_ID() - `clSessId` full column name 
* @method static string FN_CL_SESS_IP() - `clSessIp` full column name 
* @method static string FN_CL_SESS_START_AT() - `clSessStartAt` full column name 
* @method static string FN_CL_SESS_LATITUDE() - `clSessLatitude` full column name 
* @method static string FN_CL_SESS_LONGITUDE() - `clSessLongitude` full column name 
* @method static string FN_CL_SESS_COUNTRY_NAME() - `clSessCountryName` full column name 
* @method static string FN_CL_SESS_COUNTRY_CODE() - `clSessCountryCode` full column name 
* @method static string FN_CL_SESS_CITY_NAME() - `clSessCityName` full column name 
* @method static string FN_CL_SESS_REGION_NAME() - `clSessRegionName` full column name 
* @method static string FN_CL_SESS_AGENT() - `clSessAgent` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(string $clSessId, string $clSessIp, float $clSessLatitude, float $clSessLongitude, string $clSessCountryName, string $clSessCountryCode, string $clSessCityName, string $clSessRegionName, string $clSessAgent, string|datetime $clSessStartAt ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $clSessId, string $clSessIp, float $clSessLatitude, float $clSessLongitude, string $clSessCountryName, string $clSessCountryCode, string $clSessCityName, string $clSessRegionName, string $clSessAgent, string|datetime $clSessStartAt ="NOW()") add entry if not exists. check for unique column.
* */
class Sessions extends ModelBase{
    /**
    * Constant: fd cl id.
    * @var mixed
    */
    const FD_CL_ID="clId";
    /**
    * Constant: fd cl sess id.
    * @var mixed
    */
    const FD_CL_SESS_ID="clSessId";
    /**
    * Constant: fd cl sess ip.
    * @var mixed
    */
    const FD_CL_SESS_IP="clSessIp";
    /**
    * Constant: fd cl sess start at.
    * @var mixed
    */
    const FD_CL_SESS_START_AT="clSessStartAt";
    /**
    * Constant: fd cl sess latitude.
    * @var mixed
    */
    const FD_CL_SESS_LATITUDE="clSessLatitude";
    /**
    * Constant: fd cl sess longitude.
    * @var mixed
    */
    const FD_CL_SESS_LONGITUDE="clSessLongitude";
    /**
    * Constant: fd cl sess country name.
    * @var mixed
    */
    const FD_CL_SESS_COUNTRY_NAME="clSessCountryName";
    /**
    * Constant: fd cl sess country code.
    * @var mixed
    */
    const FD_CL_SESS_COUNTRY_CODE="clSessCountryCode";
    /**
    * Constant: fd cl sess city name.
    * @var mixed
    */
    const FD_CL_SESS_CITY_NAME="clSessCityName";
    /**
    * Constant: fd cl sess region name.
    * @var mixed
    */
    const FD_CL_SESS_REGION_NAME="clSessRegionName";
    /**
    * Constant: fd cl sess agent.
    * @var mixed
    */
    const FD_CL_SESS_AGENT="clSessAgent";
	/**
	* table's name
	*/
	protected $table = "%prefix%sessions";
}