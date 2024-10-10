<?php
// @author: C.A.D. BONDJE DOUE
// @file: Mails.php
// @date: 20240922 19:45:49
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Store mails</summary>
/**
* Store mails
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $mail_from from
* @property int $mail_try attemps
* @property int $mail_status staus
* @property string $mail_data mail data info
* @property string|datetime $mail_createAt ="NOW()" Now
* @property string|datetime $mail_updateAt Last try datetime
* @method static string FD_CL_ID() - `clId` full column name 
* @method static string FD_MAIL_FROM() - `mail_from` full column name 
* @method static string FD_MAIL_TRY() - `mail_try` full column name 
* @method static string FD_MAIL_STATUS() - `mail_status` full column name 
* @method static string FD_MAIL_DATA() - `mail_data` full column name 
* @method static string FD_MAIL_CREATE_AT() - `mail_createAt` full column name 
* @method static string FD_MAIL_UPDATE_AT() - `mail_updateAt` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(string $mail_from, int $mail_try, int $mail_status, string $mail_data, string|datetime $mail_updateAt, string|datetime $mail_createAt ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $mail_from, int $mail_try, int $mail_status, string $mail_data, string|datetime $mail_updateAt, string|datetime $mail_createAt ="NOW()") add entry if not exists. check for unique column.
* */
class Mails extends ModelBase{
	const FD_CL_ID="clId";
	const FD_MAIL_FROM="mail_from";
	const FD_MAIL_TRY="mail_try";
	const FD_MAIL_STATUS="mail_status";
	const FD_MAIL_DATA="mail_data";
	const FD_MAIL_CREATE_AT="mail_createAt";
	const FD_MAIL_UPDATE_AT="mail_updateAt";
	/**
	* table's name
	*/
	protected $table = "%prefix%mails";
}