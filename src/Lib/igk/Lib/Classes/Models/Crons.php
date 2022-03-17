<?php
// @author: C.A.D. BONDJE DOUE
// @file: Crons.php
// @desc: model file
// @date: 20220314 11:26:49
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* @package IGK\Models
* @property mixed $clId
* @property mixed $crons_name
* @property mixed $crons_process
* @property mixed $crons_script
* @property mixed $crons_options
* @property mixed $crons_class
* @property mixed $crons_create_at
* @property mixed $crons_update_at
*/
class Crons extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%crons";
}