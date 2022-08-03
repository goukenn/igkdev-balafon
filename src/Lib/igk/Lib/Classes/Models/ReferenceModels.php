<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReferenceModels.php
// @desc: model file
// @date: 20220802 21:32:00
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property mixed|int $clId
* @property mixed|varchar $clModel
* @property mixed|int $clNextValue*/
class ReferenceModels extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%reference_models";
}