<?php
// @author: C.A.D. BONDJE DOUE
// @file: Mails.php
// @desc: model file
// @date: 20220802 21:32:00
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @property mixed|int $clId
* @property mixed|varchar $mail_from
* @property mixed|int $mail_try
* @property mixed|int $mail_status
* @property mixed|json $mail_data
* @property mixed|datetime $mail_createAt
* @property mixed|datetime $mail_updateAt*/
class Mails extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%mails";
}