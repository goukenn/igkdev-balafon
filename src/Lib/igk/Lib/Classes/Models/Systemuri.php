<?php
// @author: C.A.D. BONDJE DOUE
// @file: Systemuri.php
// @date: 20230705 10:31:06
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>store system uri.</summary>
/**
* store system uri.
* @package IGK\Models
* @property int $clId
* @property string $clName
* @property string $clUri
* @method static ?self Add(string $clName, string $clUri) add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clUri) add entry if not exists. check for unique column.
* */
class Systemuri extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CL_NAME="clName";
	const FD_CL_URI="clUri";
	/**
	* table's name
	*/
	protected $table = "%prefix%systemuri";
}