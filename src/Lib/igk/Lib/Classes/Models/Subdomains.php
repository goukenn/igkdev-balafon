<?php
// @author: C.A.D. BONDJE DOUE
// @file: Subdomains.php
// @desc: model file
// @date: 20220116 16:24:43
namespace IGK\Models;

use IGK\Models\ModelBase;

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