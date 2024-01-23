<?php
// @author: C.A.D. BONDJE DOUE
// @file: Systemuri.php
// @date: 20240101 17:47:26
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>store system uri.</summary>
/**
* store system uri.
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
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