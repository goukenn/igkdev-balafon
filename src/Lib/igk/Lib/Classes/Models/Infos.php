<?php
// @author: C.A.D. BONDJE DOUE
// @file: Infos.php
// @desc: model file
// @date: 20220109 10:15:50
namespace IGK\Models;

use IGK\Models\ModelBase;

class Infos extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%infos"; 
	/**
	*override primary key 
	*/
	protected $primaryKey = "clName";
}