<?php
// @author: C.A.D. BONDJE DOUE
// @file: Infos.php
// @desc: model file
// @date: 20220116 16:24:43
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
	protected $displayKey = "clName";
}