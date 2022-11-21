<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReferenceModels.php
// @date: 20221121 16:32:44
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Store reference domain</summary>
/**
* Store reference domain
* @package IGK\Models
* @property int $clId
* @property string $clModel
* @property int $clNextValue
* @method static ?self Add(string $clModel, int $clNextValue) add entry helper
* @method static ?self AddIfNotExists(string $clModel, int $clNextValue) add entry if not exists. check for unique column.
* */
class ReferenceModels extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%reference_models";
}