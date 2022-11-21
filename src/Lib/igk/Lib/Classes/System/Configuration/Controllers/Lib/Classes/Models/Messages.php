<?php
// @author: C.A.D. BONDJE DOUE
// @file: Messages.php
// @date: 20221119 11:10:14
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>message loading</summary>
/**
* message loading
* @package IGK\Models
* @property int $clid
* @property string $clmessage
* @property int|?\IGK\Models\Users $msgAuthor
* @method static ?self Add(string $clmessage, int|?\IGK\Models\Users $msgAuthor) add entry helper
* @method static ?self AddIfNotExists(string $clmessage, int|?\IGK\Models\Users $msgAuthor) add entry if not exists. check for unique column.
* */
class Messages extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%messages"; 
	/**
	*override primary key 
	*/
	protected $primaryKey = "clid";
	/**
	*override refid key 
	*/
	protected $refId = "clid";
}