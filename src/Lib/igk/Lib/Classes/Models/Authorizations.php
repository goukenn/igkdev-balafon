<?php
// @author: C.A.D. BONDJE DOUE
// @file: Authorizations.php
// @date: 20260102 09:35:11
namespace IGK\Models;


use IGK\Models\ModelBase;

/**
* 
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $clName
* @property string $clController
* @property string $clDescription
* @property string|datetime $clCreate_At ="NOW()"
* @property string|datetime $clUpdate_At ="NOW()"
* @method static string FN_CL_ID() - `clId` full column name 
* @method static string FN_CL_NAME() - `clName` full column name 
* @method static string FN_CL_CONTROLLER() - `clController` full column name 
* @method static string FN_CL_DESCRIPTION() - `clDescription` full column name 
* @method static string FN_CL_CREATE_AT() - `clCreate_At` full column name 
* @method static string FN_CL_UPDATE_AT() - `clUpdate_At` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(string $clName, string $clController, string $clDescription, string|datetime $clCreate_At ="NOW()", string|datetime $clUpdate_At ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clController, string $clDescription, string|datetime $clCreate_At ="NOW()", string|datetime $clUpdate_At ="NOW()") add entry if not exists. check for unique column.
* */
class Authorizations extends ModelBase{

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
    const FD_CL_CONTROLLER="clController";

    /**
    * auto generate doc.
    * @var mixed
    */
    const FD_CL_DESCRIPTION="clDescription";

    /**
    * auto generate doc.
    * @var mixed
    */
    const FD_CL_CREATE_AT="clCreate_At";

    /**
    * auto generate doc.
    * @var mixed
    */
    const FD_CL_UPDATE_AT="clUpdate_At";
	/**
	* table's name
	*/
	protected $table = "%prefix%authorizations";

    /**
    * auto generate doc.
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