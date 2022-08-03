<?php
// @author: C.A.D. BONDJE DOUE
// @file: UsersReferenceModels.php
// @desc: model file
// @date: 20220802 21:32:00
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property mixed|int $clId
* @property mixed|TbigkUsers|int $clUser_Id
* @property mixed|varchar $clModel
* @property mixed|int $clNextValue*/
class UsersReferenceModels extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%users_reference_models";
}