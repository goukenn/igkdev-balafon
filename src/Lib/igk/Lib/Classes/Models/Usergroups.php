<?php
// @author: C.A.D. BONDJE DOUE
// @file: Usergroups.php
// @date: 20221111 21:30:07
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property int $clId
* @property int|\IGK\Models\Users $clUser_Id
* @property int|\IGK\Models\Groups $clGroup_Id
* @method static ?self Add(int|\IGK\Models\Users $clUser_Id, int|\IGK\Models\Groups $clGroup_Id) add entry helper
* @method static ?self AddIfNotExists(int|\IGK\Models\Users $clUser_Id, int|\IGK\Models\Groups $clGroup_Id) add entry if not exists. check for unique column.
* */
class Usergroups extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%usergroups";
}