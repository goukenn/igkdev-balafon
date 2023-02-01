<?php
// @author: C.A.D. BONDJE DOUE
// @file: Mails.php
// @date: 20230131 13:55:04
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
* @property string|datetime $mail_createAt ="NOW()"
* @property string|datetime $mail_updateAt
* @method static ?self Add(string $mail_from, int $mail_try, int $mail_status, string $mail_data, string|datetime $mail_updateAt, string|datetime $mail_createAt ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $mail_from, int $mail_try, int $mail_status, string $mail_data, string|datetime $mail_updateAt, string|datetime $mail_createAt ="NOW()") add entry if not exists. check for unique column.
* */
class Mails extends ModelBase{
	const FD_CL_ID="clId";
	const FD_MAILFROM="mail_from";
	const FD_MAILTRY="mail_try";
	const FD_MAILSTATUS="mail_status";
	const FD_MAILDATA="mail_data";
	const FD_MAILCREATE_AT="mail_createAt";
	const FD_MAILUPDATE_AT="mail_updateAt";
	/**
	* table's name
	*/
	protected $table = "%prefix%mails";
}