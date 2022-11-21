<?php
// @author: C.A.D. BONDJE DOUE
// @file: LaravelUsers.php
// @date: 20221119 11:10:14
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>laravel users</summary>
/**
* laravel users
* @package IGK\Models
* @property int $clid
* @property string $clLogin
* @property string $clPasswd
* @method static ?self Add(string $clLogin, string $clPasswd) add entry helper
* @method static ?self AddIfNotExists(string $clLogin, string $clPasswd) add entry if not exists. check for unique column.
* */
class LaravelUsers extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%LaravelUsers"; 
	/**
	*override primary key 
	*/
	protected $primaryKey = "clid";
	/**
	*override refid key 
	*/
	protected $refId = "clid";
}