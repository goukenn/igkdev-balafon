<?php
// @author: C.A.D. BONDJE DOUE
// @file: Mailinfo.php
// @date: 20221119 11:10:14
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>store - connect mail info</summary>
/**
* store - connect mail info
* @package IGK\Models
* @property int $clid
* @property string $mli_gender ="M"
* @property string $mli_email
* @property string $mli_firstname
* @property string $mli_lastname
* @property string $mli_birthday
* @property string $mli_picture
* @property int $mli_verified
* @property int $mli_status
* @property string $mli_provider
* @property string $mli_provider_id
* @property string|datetime $mli_create_at
* @property string|datetime $mli_update_at
* @method static ?self Add(string $mli_email, string $mli_firstname, string $mli_lastname, string $mli_birthday, string $mli_picture, int $mli_verified, int $mli_status, string $mli_provider, string $mli_provider_id, string|datetime $mli_create_at, string|datetime $mli_update_at, string $mli_gender ="M") add entry helper
* @method static ?self AddIfNotExists(string $mli_email, string $mli_firstname, string $mli_lastname, string $mli_birthday, string $mli_picture, int $mli_verified, int $mli_status, string $mli_provider, string $mli_provider_id, string|datetime $mli_create_at, string|datetime $mli_update_at, string $mli_gender ="M") add entry if not exists. check for unique column.
* */
class Mailinfo extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%mailinfo"; 
	/**
	*override primary key 
	*/
	protected $primaryKey = "clid";
	/**
	*override refid key 
	*/
	protected $refId = "clid";
}