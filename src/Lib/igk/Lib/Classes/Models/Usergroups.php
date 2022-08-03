<?php
// @author: C.A.D. BONDJE DOUE
// @file: Usergroups.php
// @desc: model file
// @date: 20220802 21:32:00
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property mixed|int $clId
* @property mixed|TbigkUsers|int $clUser_Id
* @property mixed|TbigkGroups|int $clGroup_Id*/
class Usergroups extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%usergroups";
}