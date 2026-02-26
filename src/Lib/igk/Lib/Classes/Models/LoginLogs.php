<?php
// @author: C.A.D. BONDJE DOUE
// @file: LoginLogs.php
// @date: 20260102 09:35:11
namespace IGK\Models;


use IGK\Models\ModelBase;

/**
* Store connection history
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
* @method static string FN_LOGLOGS_ID() - `loglogs_Id` full column name 
* @method static string FN_LOGLOGS_USER_GUID() - `loglogs_UserGuid` full column name 
* @method static string FN_LOGLOGS_AGENT() - `loglogs_Agent` full column name 
* @method static string FN_LOGLOGS_IP() - `loglogs_IP` full column name 
* @method static string FN_LOGLOGS_GEO_X() - `loglogs_GeoX` full column name 
* @method static string FN_LOGLOGS_GEO_Y() - `loglogs_GeoY` full column name 
* @method static string FN_LOGLOGS_REGION() - `loglogs_Region` full column name 
* @method static string FN_LOGLOGS_CODE() - `loglogs_Code` full column name 
* @method static string FN_LOGLOGS_COUNTRY_NAME() - `loglogs_CountryName` full column name 
* @method static string FN_LOGLOGS_CITY() - `loglogs_City` full column name 
* @method static string FN_LOGLOGS_STATUS() - `loglogs_Status` full column name 
* @method static string FN_LOGLOGS_DESCRIPTION() - `loglogs_Description` full column name 
* @method static string FN_LOGLOGS_CREATE_AT() - `loglogs_Create_At` full column name 
* @method static string FN_LOGLOGS_UPDATE_AT() - `loglogs_Update_At` full column name 
* @method static ?array joinOnLoglogsId($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnLoglogsId() - macros function
* @method static ?self Add(string|?\IGK\Models\Users $loglogs_UserGuid, string $loglogs_Agent, string $loglogs_IP, float $loglogs_GeoX, float $loglogs_GeoY, string $loglogs_Region, string $loglogs_Code, string $loglogs_CountryName, string $loglogs_City, int $loglogs_Status, string $loglogs_Description, string|datetime $regLinkCreate_At ="NOW()", string|datetime $regLinkUpdate_At ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string|?\IGK\Models\Users $loglogs_UserGuid, string $loglogs_Agent, string $loglogs_IP, float $loglogs_GeoX, float $loglogs_GeoY, string $loglogs_Region, string $loglogs_Code, string $loglogs_CountryName, string $loglogs_City, int $loglogs_Status, string $loglogs_Description, string|datetime $regLinkCreate_At ="NOW()", string|datetime $regLinkUpdate_At ="NOW()") add entry if not exists. check for unique column.
* */
class LoginLogs extends ModelBase{

    /**
    * auto generate doc.
    * @var mixed
    */
    const FD_LOGLOGS_ID="loglogs_Id";

    /**
    * auto generate doc.
    * @var mixed
    */
    const FD_LOGLOGS_USER_GUID="loglogs_UserGuid";

    /**
    * auto generate doc.
    * @var mixed
    */
    const FD_LOGLOGS_AGENT="loglogs_Agent";

    /**
    * auto generate doc.
    * @var mixed
    */
    const FD_LOGLOGS_IP="loglogs_IP";

    /**
    * auto generate doc.
    * @var mixed
    */
    const FD_LOGLOGS_GEO_X="loglogs_GeoX";

    /**
    * auto generate doc.
    * @var mixed
    */
    const FD_LOGLOGS_GEO_Y="loglogs_GeoY";

    /**
    * auto generate doc.
    * @var mixed
    */
    const FD_LOGLOGS_REGION="loglogs_Region";

    /**
    * auto generate doc.
    * @var mixed
    */
    const FD_LOGLOGS_CODE="loglogs_Code";

    /**
    * auto generate doc.
    * @var mixed
    */
    const FD_LOGLOGS_COUNTRY_NAME="loglogs_CountryName";

    /**
    * auto generate doc.
    * @var mixed
    */
    const FD_LOGLOGS_CITY="loglogs_City";

    /**
    * auto generate doc.
    * @var mixed
    */
    const FD_LOGLOGS_STATUS="loglogs_Status";

    /**
    * auto generate doc.
    * @var mixed
    */
    const FD_LOGLOGS_DESCRIPTION="loglogs_Description";

    /**
    * auto generate doc.
    * @var mixed
    */
    const FD_LOGLOGS_CREATE_AT="loglogs_Create_At";

    /**
    * auto generate doc.
    * @var mixed
    */
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