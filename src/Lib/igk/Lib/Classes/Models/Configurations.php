<?php
// @author: C.A.D. BONDJE DOUE
// @file: Configurations.php
// @date: 20240101 17:47:26
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $clName
* @property string $clValue
* @property string $clDescription
* @method static ?self Add(string $clName, string $clValue, string $clDescription) add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clValue, string $clDescription) add entry if not exists. check for unique column.
* */
class Configurations extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CL_NAME="clName";
	const FD_CL_VALUE="clValue";
	const FD_CL_DESCRIPTION="clDescription";
	/**
	* table's name
	*/
	protected $table = "%prefix%configurations";
}