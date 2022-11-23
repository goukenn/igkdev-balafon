<?php
// @author: C.A.D. BONDJE DOUE
// @file: Humans.php
// @date: 20221123 12:07:49
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>store human list</summary>
/**
* store human list
* @package IGK\Models
* @property int $clId
* @property string $clGender
* @property string $clFirstName
* @property string $clLastName
* @method static ?self Add(string $clGender, string $clFirstName, string $clLastName) add entry helper
* @method static ?self AddIfNotExists(string $clGender, string $clFirstName, string $clLastName) add entry if not exists. check for unique column.
* */
class Humans extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%humans";
}