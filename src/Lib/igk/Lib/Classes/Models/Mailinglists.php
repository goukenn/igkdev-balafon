<?php
// @author: C.A.D. BONDJE DOUE
// @file: Mailinglists.php
// @desc: model file
// @date: 20220802 21:32:00
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property mixed|int $clId
* @property mixed|varchar $clEmail
* @property mixed|int $clState
* @property mixed|text $clSource
* @property mixed|varchar $clml_locale
* @property mixed|text $clml_agent
* @property mixed|datetime $clml_create_at
* @property mixed|datetime $clml_update_at*/
class Mailinglists extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%mailinglists";
}