<?php
// @author: C.A.D. BONDJE DOUE
// @file: Sessions.php
// @desc: model file
// @date: 20220222 03:33:08
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* @package IGK\Models
* @property mixed $clId
* @property mixed $clSessId
* @property mixed $clSessIp
* @property mixed $clSessStartAt
* @property mixed $clSessLatitude
* @property mixed $clSessLongitude
* @property mixed $clSessCountryName
* @property mixed $clSessCountryCode
* @property mixed $clSessCityName
* @property mixed $clSessRegionName
* @property mixed $clSessAgent
*/
class Sessions extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%sessions";
}