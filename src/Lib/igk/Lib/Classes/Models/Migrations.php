<?php
// @author: C.A.D. BONDJE DOUE
// @file: Migrations.php
// @desc: model file
// @date: 20220109 10:15:50
namespace IGK\Models;

use IGK\Models\ModelBase;

class Migrations extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%migrations";
}