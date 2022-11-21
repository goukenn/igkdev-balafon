<?php
// @author: C.A.D. BONDJE DOUE
// @file: References.php
// @date: 20221119 11:10:14
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>laravel users</summary>
/**
* laravel users
* @package IGK\Models
* @property int $clid
* */
class References extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%references"; 
	/**
	*override primary key 
	*/
	protected $primaryKey = "clid";
	/**
	*override refid key 
	*/
	protected $refId = "clid";
}