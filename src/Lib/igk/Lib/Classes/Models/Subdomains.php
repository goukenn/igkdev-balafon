<?php
// @author: C.A.D. BONDJE DOUE
// @file: Subdomains.php
// @date: 20230617 00:34:40
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>store sub domain</summary>
/**
* store sub domain
* @package IGK\Models
* @property int $clId
* @property string $clName
* @property string $clCtrl
* @property string $clView
* @method static ?self Add(string $clName, string $clCtrl, string $clView) add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clCtrl, string $clView) add entry if not exists. check for unique column.
* */
class Subdomains extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CL_NAME="clName";
	const FD_CL_CTRL="clCtrl";
	const FD_CL_VIEW="clView";
	/**
	* table's name
	*/
	protected $table = "%prefix%subdomains";
}