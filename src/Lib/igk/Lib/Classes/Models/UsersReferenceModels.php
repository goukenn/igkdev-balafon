<?php
// @author: C.A.D. BONDJE DOUE
// @file: UsersReferenceModels.php
// @desc: model file
// @date: 20220705 14:13:39
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property mixed $clId
* @property mixed $clUser_Id
* @property mixed $clModel
* @property mixed $clNextValue*/
class UsersReferenceModels extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%users_reference_models";
}