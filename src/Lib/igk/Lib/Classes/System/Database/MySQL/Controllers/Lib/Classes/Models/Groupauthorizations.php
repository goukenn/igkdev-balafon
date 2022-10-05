<?php
// @author: C.A.D. BONDJE DOUE
// @file: Groupauthorizations.php
// @date: 20220915 17:51:19
namespace IGK\Models;

use IGK\Models\ModelBase;

///<summary>Store framework group authorisation</summary>
/**
* Store framework group authorisation
* @package IGK\Models
* @property int $clId
* @property int|\IGK\Models\Groups $clGroup_Id
* @property int|\IGK\Models\Authorizations $clAuth_Id
* @property string $clGrant
* @method static ?self Add(int|\IGK\Models\Groups $clGroup_Id, int|\IGK\Models\Authorizations $clAuth_Id, string $clGrant) add entry helper
* @method static ?self AddIfNotExists(int|\IGK\Models\Groups $clGroup_Id, int|\IGK\Models\Authorizations $clAuth_Id, string $clGrant) add entry if not exists. check for unique column.
* */
class Groupauthorizations extends ModelBase{
	/**
	* table's name
	*/
	protected $table = "%prefix%groupauthorizations";
}