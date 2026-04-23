<?php
// @author: C.A.D. BONDJE DOUE
// @file: Usergroups.php
// @date: 20260102 09:35:11
namespace IGK\Models;
use IGK\Models\ModelBase;

/**
* auto generate doc.
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @method static ?self AddIfNotExists(int|\IGK\Models\Users $clUser_Id
*/
class Usergroups extends ModelBase{
    /**
    * Constant: fd cl id.
    * @var mixed
    */
    const FD_CL_ID="clId";
    /**
    * Constant: fd cl user id.
    * @var mixed
    */
    const FD_CL_USER_ID="clUser_Id";
    /**
    * Constant: fd cl group id.
    * @var mixed
    */
    const FD_CL_GROUP_ID="clGroup_Id";
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
	protected $table = "%prefix%usergroups";
    /**
    * Property: unique columns.
    * @var mixed
    */
    protected $unique_columns = array (
	  0 => 
	  array (
	    0 => 'clUser_Id',
	    1 => 'clGroup_Id',
	  ),
	);
}