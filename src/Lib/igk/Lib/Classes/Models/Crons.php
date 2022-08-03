<?php
// @author: C.A.D. BONDJE DOUE
// @file: Crons.php
// @desc: model file
// @date: 20220802 21:32:00
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property mixed|int $clId
* @property mixed|varchar $crons_name
* @property mixed|varchar $crons_process
* @property mixed|text $crons_script
* @property mixed|json $crons_options
* @property mixed|text $crons_class
* @property mixed|datetime $crons_create_at
* @property mixed|datetime $crons_update_at*/
class Crons extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%crons";
}