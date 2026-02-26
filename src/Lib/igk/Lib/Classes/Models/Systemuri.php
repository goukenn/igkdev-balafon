<?php
// @author: C.A.D. BONDJE DOUE
// @file: Systemuri.php
// @date: 20260102 09:35:11
namespace IGK\Models;


use IGK\Models\ModelBase;

/**
* store system uri.
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $clName
* @property string $clUri
* @method static string FN_CL_ID() - `clId` full column name 
* @method static string FN_CL_NAME() - `clName` full column name 
* @method static string FN_CL_URI() - `clUri` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(string $clName, string $clUri) add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clUri) add entry if not exists. check for unique column.
* */
class Systemuri extends ModelBase{

    /**
    * auto generate doc.
    * @var mixed
    */
    const FD_CL_ID="clId";

    /**
    * auto generate doc.
    * @var mixed
    */
    const FD_CL_NAME="clName";

    /**
    * auto generate doc.
    * @var mixed
    */
    const FD_CL_URI="clUri";
	/**
	* table's name
	*/
	protected $table = "%prefix%systemuri";
}