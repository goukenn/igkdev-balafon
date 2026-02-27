<?php
// @author: C.A.D. BONDJE DOUE
// @file: Authorizations.php
// @date: 20260102 09:35:11
namespace IGK\Models;


use IGK\Models\ModelBase;

/**
* auto generate doc.
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @method static ?self AddIfNotExists(string $clName
*/
class Authorizations extends ModelBase{

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
    * Constant: fd cl controller.
    * @var mixed
    */
    const FD_CL_CONTROLLER="clController";

    /**
    * Constant: fd cl description.
    * @var mixed
    */
    const FD_CL_DESCRIPTION="clDescription";

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
	protected $table = "%prefix%authorizations";

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