<?php
// @author: C.A.D. BONDJE DOUE
// @file: Guids.php
// @desc: model file
// @date: 20220705 14:13:39
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property mixed $clId
* @property mixed $clGUID
* @property mixed $clDesc
* @property mixed $clCreateAt*/
class Guids extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%guids";
}