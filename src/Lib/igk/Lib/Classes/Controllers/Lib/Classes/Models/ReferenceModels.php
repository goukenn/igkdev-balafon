<?php
// @author: C.A.D. BONDJE DOUE
// @date: 20211220 01:45:33
namespace IGK\Controllers\IGK\Models;

use IGK\Controllers\IGK\Models\ModelBase;

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