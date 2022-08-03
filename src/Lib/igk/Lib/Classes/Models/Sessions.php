<?php
// @author: C.A.D. BONDJE DOUE
// @file: Sessions.php
// @desc: model file
// @date: 20220802 21:32:00
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property mixed|int $clId
* @property mixed|varchar $clSessId
* @property mixed|varchar $clSessIp
* @property mixed|datetime $clSessStartAt
* @property mixed|float $clSessLatitude
* @property mixed|float $clSessLongitude
* @property mixed|varchar $clSessCountryName
* @property mixed|varchar $clSessCountryCode
* @property mixed|varchar $clSessCityName
* @property mixed|varchar $clSessRegionName
* @property mixed|text $clSessAgent*/
class Sessions extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%sessions";
}