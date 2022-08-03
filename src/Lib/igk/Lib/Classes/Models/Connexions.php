<?php
// @author: C.A.D. BONDJE DOUE
// @file: Connexions.php
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
* @property mixed|datetime $clDateTime
* @property mixed|varchar $clFrom*/
class Connexions extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%connexions";
}