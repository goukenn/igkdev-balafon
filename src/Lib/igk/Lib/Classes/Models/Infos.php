<?php
// @author: C.A.D. BONDJE DOUE
// @file: Infos.php
// @date: 20230617 00:34:40
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property int $clId
* @property string $clName
* @property string $clValue
* @method static ?self Add(string $clName, string $clValue) add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clValue) add entry if not exists. check for unique column.
* */
class Infos extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CL_NAME="clName";
	const FD_CL_VALUE="clValue";
	/**
	* table's name
	*/
	protected $table = "%prefix%infos";
}