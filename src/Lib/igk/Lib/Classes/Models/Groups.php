<?php
// @author: C.A.D. BONDJE DOUE
// @file: Groups.php
// @date: 20260102 09:35:11
namespace IGK\Models;
use IGK\Models\ModelBase;

/**
* Store framework groups
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $clName
* @property string $clDescription
* @property string $clController
* @property string|datetime $clCreate_At ="NOW()"
* @property string|datetime $clUpdate_At ="NOW()"
* @method static string FN_CL_ID() - `clId` full column name 
* @method static string FN_CL_NAME() - `clName` full column name 
* @method static string FN_CL_DESCRIPTION() - `clDescription` full column name 
* @method static string FN_CL_CONTROLLER() - `clController` full column name 
* @method static string FN_CL_CREATE_AT() - `clCreate_At` full column name 
* @method static string FN_CL_UPDATE_AT() - `clUpdate_At` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(string $clName, string $clDescription, string $clController, string|datetime $clCreate_At ="NOW()", string|datetime $clUpdate_At ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clDescription, string $clController, string|datetime $clCreate_At ="NOW()", string|datetime $clUpdate_At ="NOW()") add entry if not exists. check for unique column.
* */
class Groups extends ModelBase{
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
    * Constant: fd cl description.
    * @var mixed
    */
    const FD_CL_DESCRIPTION="clDescription";
    /**
    * Constant: fd cl controller.
    * @var mixed
    */
    const FD_CL_CONTROLLER="clController";
    /**
    * Constant: fd cl create at.
    * @var mixed
    */
    const FD_CL_CREATE_AT="clCreate_At";
    /**
    * Constant: fd cl update at.
    * @var mixed
    */
    const FD_CL_UPDATE_AT="clUpdate_At";
	/**
	* table's name
	*/
	protected $table = "%prefix%groups";
    /**
    * Property: unique columns.
    * @var mixed
    */
    protected $unique_columns = array (
	  0 => 
	  array (
	    0 => 'clName',
	    1 => 'clController',
	  ),
	);
}