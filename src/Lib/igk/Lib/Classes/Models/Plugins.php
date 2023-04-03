<?php
// @author: C.A.D. BONDJE DOUE
// @file: Plugins.php
// @date: 20230310 13:01:24
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>store plugins.</summary>
/**
* store plugins.
* @package IGK\Models
* @property int $clId
* @property string $clName
* @property string $clEmail
* @property string|datetime $clRelease
* @property string $clVersion
* @method static ?self Add(string $clName, string $clEmail, string|datetime $clRelease, string $clVersion) add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clEmail, string|datetime $clRelease, string $clVersion) add entry if not exists. check for unique column.
* */
class Plugins extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CL_NAME="clName";
	const FD_CL_EMAIL="clEmail";
	const FD_CL_RELEASE="clRelease";
	const FD_CL_VERSION="clVersion";
	/**
	* table's name
	*/
	protected $table = "%prefix%plugins";
}