<?php
// @author: C.A.D. BONDJE DOUE
// @file: UserInfos.php
// @date: 20260102 09:35:11
namespace IGK\Models;


use IGK\Models\ModelBase;

/**
* auto generate doc.
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property string $clDescription description of that value
* @method static ?self AddIfNotExists(int|\IGK\Models\Users $clUser_Id
*/
class UserInfos extends ModelBase{

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
    * Constant: fd cl user info type id.
    * @var mixed
    */
    const FD_CL_USER_INFO_TYPE_ID="clUserInfoType_Id";

    /**
    * Constant: fd cl value.
    * @var mixed
    */
    const FD_CL_VALUE="clValue";

    /**
    * Constant: fd cl description.
    * @var mixed
    */
    const FD_CL_DESCRIPTION="clDescription";
	/**
	* table's name
	*/
	protected $table = "%prefix%user_infos";
}