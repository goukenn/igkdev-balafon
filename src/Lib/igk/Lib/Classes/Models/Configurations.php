<?php
// @author: C.A.D. BONDJE DOUE
// @file: Configurations.php
// @desc: model file
// @date: 20220109 10:15:50
namespace IGK\Models;

use IGK\Models\ModelBase;

class Configurations extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%configurations"; 
	/**
	*override primary key 
	*/
	protected $primaryKey = "clName";
}