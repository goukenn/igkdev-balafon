<?php
// @author: C.A.D. BONDJE DOUE
// @file: Partners.php
// @date: 20230705 10:31:06
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>store local sites partner.</summary>
/**
* store local sites partner.
* @package IGK\Models
* @property int $clId
* @property string $clName
* @property string $clCategory
* @property string $clWebSite
* @property string $clDescription
* @method static ?self Add(string $clName, string $clCategory, string $clWebSite, string $clDescription) add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clCategory, string $clWebSite, string $clDescription) add entry if not exists. check for unique column.
* */
class Partners extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CL_NAME="clName";
	const FD_CL_CATEGORY="clCategory";
	const FD_CL_WEB_SITE="clWebSite";
	const FD_CL_DESCRIPTION="clDescription";
	/**
	* table's name
	*/
	protected $table = "%prefix%partners";
}