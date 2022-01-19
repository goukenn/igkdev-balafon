<?php
// @author: C.A.D. BONDJE DOUE
// @file: UsersReferenceModels.php
// @desc: model file
// @date: 20220116 16:24:43
namespace IGK\Models;

use IGK\Models\ModelBase;

class UsersReferenceModels extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%users_reference_models"; 
	/**
	*override primary key 
	*/
	protected $primaryKey = "clModel";
}