<?php
// @author: C.A.D. BONDJE DOUE
// @file: Sessions.php
// @date: 20221203 14:34:18
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Track user started session</summary>
/**
* Track user started session
* @package IGK\Models
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
* @method static ?self Add(string $clSessId, string $clSessIp, float $clSessLatitude, float $clSessLongitude, string $clSessCountryName, string $clSessCountryCode, string $clSessCityName, string $clSessRegionName, string $clSessAgent, string|datetime $clSessStartAt ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $clSessId, string $clSessIp, float $clSessLatitude, float $clSessLongitude, string $clSessCountryName, string $clSessCountryCode, string $clSessCityName, string $clSessRegionName, string $clSessAgent, string|datetime $clSessStartAt ="NOW()") add entry if not exists. check for unique column.
* */
class Sessions extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%sessions";
}