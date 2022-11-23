<?php
// @author: C.A.D. BONDJE DOUE
// @file: Partners.php
// @date: 20221123 12:07:49
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
	/**
	* table's name
	*/
	protected $table = "%prefix%partners";
}