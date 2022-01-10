<?php
// @author: C.A.D. BONDJE DOUE
// @file: Systemuri.php
// @desc: model file
// @date: 20220109 10:15:50
namespace IGK\Models;

use IGK\Models\ModelBase;

class Systemuri extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%systemuri"; 
	/**
	*override primary key 
	*/
	protected $primaryKey = "clName";
}