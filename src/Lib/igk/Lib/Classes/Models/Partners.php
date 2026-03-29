<?php
// @author: C.A.D. BONDJE DOUE
// @file: Partners.php
// @date: 20260102 09:35:11
namespace IGK\Models;
use IGK\Models\ModelBase;
/**
* store local sites partner.
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $clName
* @property string $clCategory
* @property string $clWebSite
* @property string $clDescription
* @method static string FN_CL_ID() - `clId` full column name 
* @method static string FN_CL_NAME() - `clName` full column name 
* @method static string FN_CL_CATEGORY() - `clCategory` full column name 
* @method static string FN_CL_WEB_SITE() - `clWebSite` full column name 
* @method static string FN_CL_DESCRIPTION() - `clDescription` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(string $clName, string $clCategory, string $clWebSite, string $clDescription) add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clCategory, string $clWebSite, string $clDescription) add entry if not exists. check for unique column.
* */
class Partners extends ModelBase{
    /**
    * Constant: fd cl id.
    * @var mixed
    */
    const FD_CL_ID="clId";
    /**
    * Constant: fd cl name.
    * @var mixed
    */
    const FD_CL_NAME="clName";
    /**
    * Constant: fd cl category.
    * @var mixed
    */
    const FD_CL_CATEGORY="clCategory";
    /**
    * Constant: fd cl web site.
    * @var mixed
    */
    const FD_CL_WEB_SITE="clWebSite";
    /**
    * Constant: fd cl description.
    * @var mixed
    */
    const FD_CL_DESCRIPTION="clDescription";
	/**
	* table's name
	*/
	protected $table = "%prefix%partners";
}