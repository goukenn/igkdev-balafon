<?php
// @author: C.A.D. BONDJE DOUE
// @file: Groupauthorizations.php
// @desc: model file
// @date: 20220705 14:13:39
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property mixed $clId
* @property mixed $clGroup_Id
* @property mixed $clAuth_Id
* @property mixed $clGrant*/
class Groupauthorizations extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%groupauthorizations";
}