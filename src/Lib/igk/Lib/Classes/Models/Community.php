<?php
// @author: C.A.D. BONDJE DOUE
// @file: Community.php
// @date: 20230922 00:42:27
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property int $clId
* @property string $clName identifier of the community
* @property string $clValueType type of data associated to value
* @method static ?self Add(string $clName, string $clValueType) add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clValueType) add entry if not exists. check for unique column.
* */
class Community extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CL_NAME="clName";
	const FD_CL_VALUE_TYPE="clValueType";
	/**
	* table's name
	*/
	protected $table = "%prefix%community";
}