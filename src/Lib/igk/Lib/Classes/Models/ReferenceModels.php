<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReferenceModels.php
// @desc: model file
// @date: 20220109 10:15:50
namespace IGK\Models;

use IGK\Models\ModelBase;

class ReferenceModels extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%reference_models"; 
	/**
	*override primary key 
	*/
	protected $primaryKey = "clModel";
}