<?php
// @author: C.A.D. BONDJE DOUE
// @file: Connexions.php
// @date: 20230310 13:01:24
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Store started connexions</summary>
/**
* Store started connexions
* @package IGK\Models
* @property int $clId
* @property int|\IGK\Models\Users $clUser_Id
* @property string|datetime $clDateTime
* @property string $clFrom
* @method static ?self Add(int|\IGK\Models\Users $clUser_Id, string|datetime $clDateTime, string $clFrom) add entry helper
* @method static ?self AddIfNotExists(int|\IGK\Models\Users $clUser_Id, string|datetime $clDateTime, string $clFrom) add entry if not exists. check for unique column.
* */
class Connexions extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CL_USER_ID="clUser_Id";
	const FD_CL_DATE_TIME="clDateTime";
	const FD_CL_FROM="clFrom";
	/**
	* table's name
	*/
	protected $table = "%prefix%connexions";
}