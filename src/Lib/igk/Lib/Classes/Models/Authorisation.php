<?php
// @author: C.A.D. BONDJE DOUE
// @file: Authorisation.php
// @desc: model file
// @date: 20220605 03:56:41
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property mixed $clController*/
class Authorisation extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%authorisation"; 
	/**
	*override primary key 
	*/
	protected $primaryKey = "";
}