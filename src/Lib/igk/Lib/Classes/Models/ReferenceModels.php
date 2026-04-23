<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReferenceModels.php
// @date: 20260102 09:35:11
namespace IGK\Models;
use IGK\Models\ModelBase;

/**
* Store reference domain
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $clModel
* @property int $clNextValue
* @method static string FN_CL_ID() - `clId` full column name 
* @method static string FN_CL_MODEL() - `clModel` full column name 
* @method static string FN_CL_NEXT_VALUE() - `clNextValue` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(string $clModel, int $clNextValue) add entry helper
* @method static ?self AddIfNotExists(string $clModel, int $clNextValue) add entry if not exists. check for unique column.
* @method static mixed get_ref_nextnumber(int $uid,string $modelname) macros function
* @method static ?\IGK\Models\ReferenceModels update_ref_nextnumber(int $uid,string $modelname) macros function
* */
class ReferenceModels extends ModelBase{
    /**
    * Constant: fd cl id.
    * @var mixed
    */
    const FD_CL_ID="clId";
    /**
    * Constant: fd cl model.
    * @var mixed
    */
    const FD_CL_MODEL="clModel";
    /**
    * Constant: fd cl next value.
    * @var mixed
    */
    const FD_CL_NEXT_VALUE="clNextValue";
	/**
	* table's name
	*/
	protected $table = "%prefix%reference_models";
}