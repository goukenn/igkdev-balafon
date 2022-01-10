<?php
// @author: C.A.D. BONDJE DOUE
// @file: Subdomains.php
// @desc: model file
// @date: 20220109 10:15:50
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