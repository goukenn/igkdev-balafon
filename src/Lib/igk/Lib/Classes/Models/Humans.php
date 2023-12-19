<?php
// @author: C.A.D. BONDJE DOUE
// @file: Humans.php
// @date: 20231219 13:50:52
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>store human list</summary>
/**
* store human list
* @package IGK\Models
* @property int $clId
* @property string $clGender m or f for male or female
* @property string $clFirstName
* @property string $clLastName
* @method static ?self Add(string $clGender, string $clFirstName, string $clLastName) add entry helper
* @method static ?self AddIfNotExists(string $clGender, string $clFirstName, string $clLastName) add entry if not exists. check for unique column.
* */
class Humans extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CL_GENDER="clGender";
	const FD_CL_FIRST_NAME="clFirstName";
	const FD_CL_LAST_NAME="clLastName";
	/**
	* table's name
	*/
	protected $table = "%prefix%humans";
}