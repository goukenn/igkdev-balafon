<?php
// @author: C.A.D. BONDJE DOUE
// @date: 20211220 01:45:33
namespace IGK\Controllers\IGK\Models;

use IGK\Controllers\IGK\Models\ModelBase;

class Subdomains extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%subdomains"; 
		/**
	*override primary key 
	*/
		protected $primaryKey = "clName";
}