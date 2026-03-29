<?php
// @author: C.A.D. BONDJE DOUE
// @file: UsersReferenceModels.php
// @date: 20260102 09:35:11
namespace IGK\Models;
use IGK\Models\ModelBase;
/**
* auto generate doc.
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clNextValue
* @method static ?self AddIfNotExists(int|\IGK\Models\Users $clUser_Id
*/
class UsersReferenceModels extends ModelBase{
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
	protected $table = "%prefix%users_reference_models";
}