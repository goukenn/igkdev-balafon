<?php
// @author: C.A.D. BONDJE DOUE
// @file: Mails.php
// @desc: model file
// @date: 20220222 03:33:09
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* @package IGK\Models
* @property mixed $clId
* @property mixed $mail_from
* @property mixed $mail_try
* @property mixed $mail_status
* @property mixed $mail_data
* @property mixed $mail_createAt
* @property mixed $mail_updateAt
*/
class Mails extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%mails";
}