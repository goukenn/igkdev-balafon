<?php
// @author: C.A.D. BONDJE DOUE
// @file: UsersReferenceModels.php
// @date: 20221119 11:10:14
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property int $clId
* @property int|\IGK\Models\Users $clUser_Id
* @property string $clModel
* @property int $clNextValue
* @method static ?self Add(int|\IGK\Models\Users $clUser_Id, string $clModel, int $clNextValue) add entry helper
* @method static ?self AddIfNotExists(int|\IGK\Models\Users $clUser_Id, string $clModel, int $clNextValue) add entry if not exists. check for unique column.
* */
class UsersReferenceModels extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%users_reference_models";
}