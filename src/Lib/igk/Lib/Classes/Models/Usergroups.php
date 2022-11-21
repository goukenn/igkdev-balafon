<?php
// @author: C.A.D. BONDJE DOUE
// @file: Usergroups.php
// @date: 20221121 16:32:44
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property int $clId
* @property int|\IGK\Models\Users $clUser_Id
* @property int|\IGK\Models\Groups $clGroup_Id
* @property string|datetime $clCreate_at ="NOW()"
* @property string|datetime $clUpdate_at ="NOW()"
* @method static ?self Add(int|\IGK\Models\Users $clUser_Id, int|\IGK\Models\Groups $clGroup_Id, string|datetime $clCreate_at ="NOW()", string|datetime $clUpdate_at ="NOW()") add entry helper
* @method static ?self AddIfNotExists(int|\IGK\Models\Users $clUser_Id, int|\IGK\Models\Groups $clGroup_Id, string|datetime $clCreate_at ="NOW()", string|datetime $clUpdate_at ="NOW()") add entry if not exists. check for unique column.
* */
class Usergroups extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%usergroups";
}