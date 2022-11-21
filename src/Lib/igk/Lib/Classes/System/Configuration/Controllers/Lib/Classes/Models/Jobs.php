<?php
// @author: C.A.D. BONDJE DOUE
// @file: Jobs.php
// @date: 20221119 11:10:14
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>laravel users</summary>
/**
* laravel users
* @package IGK\Models
* @property int $clid
* @property string $jobstitle
* @method static ?self Add(string $jobstitle) add entry helper
* @method static ?self AddIfNotExists(string $jobstitle) add entry if not exists. check for unique column.
* */
class Jobs extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%jobs"; 
	/**
	*override primary key 
	*/
	protected $primaryKey = "clid";
	/**
	*override refid key 
	*/
	protected $refId = "clid";
}