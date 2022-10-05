<?php
// @author: C.A.D. BONDJE DOUE
// @file: Mails.php
// @date: 20220915 17:51:19
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary>Store mails</summary>
/**
* Store mails
* @package IGK\Models
* @property int $clId
* @property string $mail_from
* @property int $mail_try
* @property int $mail_status
* @property string $mail_data
* @property string|datetime $mail_createAt
* @property string|datetime $mail_updateAt
* @method static ?self Add(string $mail_from, int $mail_try, int $mail_status, string $mail_data, string|datetime $mail_updateAt, string|datetime $mail_createAt) add entry helper
* @method static ?self AddIfNotExists(string $mail_from, int $mail_try, int $mail_status, string $mail_data, string|datetime $mail_updateAt, string|datetime $mail_createAt) add entry if not exists. check for unique column.
* */
class Mails extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%mails";
}