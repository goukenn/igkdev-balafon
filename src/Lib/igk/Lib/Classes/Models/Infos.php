<?php
// @author: C.A.D. BONDJE DOUE
// @file: Infos.php
// @date: 20260824 18:08:42
namespace IGK\Models;


use IGK\Models\ModelBase;
/**
* auto generate doc.
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $clName
* @property string $clValue
* @method static string FN_CL_ID() - `clId` full column name
* @method static string FN_CL_NAME() - `clName` full column name
* @method static string FN_CL_VALUE() - `clValue` full column name
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(string $clName, string $clValue) add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clValue) add entry if not exists. check for unique column.
*/
/**
* auto generate doc.
* @package IGK\Models
*/
class Infos extends ModelBase{
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    const FD_CL_ID="clId";
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    const FD_CL_NAME="clName";
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    const FD_CL_VALUE="clValue";
	/**
	* table's name
	*/
	protected $table = "%prefix%infos";
}