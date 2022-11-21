<?php
// @author: C.A.D. BONDJE DOUE
// @file: Presentation.php
// @date: 20221119 11:10:14
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>demo presentation</summary>
/**
* demo presentation
* @package IGK\Models
* @property int $clid
* */
class Presentation extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%presentation"; 
	/**
	*override primary key 
	*/
	protected $primaryKey = "clid";
	/**
	*override refid key 
	*/
	protected $refId = "clid";
}