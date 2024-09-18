<?php
// @author: C.A.D. BONDJE DOUE
// @file: CookieStorages.php
// @date: 20240918 08:19:26
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $clIdentifier
* @property string $clName
* @property string|datetime $clDateTime
* @method static string FD_CL_ID() - `clId` full column name 
* @method static string FD_CL_IDENTIFIER() - `clIdentifier` full column name 
* @method static string FD_CL_NAME() - `clName` full column name 
* @method static string FD_CL_DATE_TIME() - `clDateTime` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(string $clIdentifier, string $clName, string|datetime $clDateTime) add entry helper
* @method static ?self AddIfNotExists(string $clIdentifier, string $clName, string|datetime $clDateTime) add entry if not exists. check for unique column.
* */
class CookieStorages extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CL_IDENTIFIER="clIdentifier";
	const FD_CL_NAME="clName";
	const FD_CL_DATE_TIME="clDateTime";
	/**
	* table's name
	*/
	protected $table = "%prefix%cookie_storages";
}