<?php
// @author: C.A.D. BONDJE DOUE
// @file: CookieStorages.php
// @date: 20260102 09:35:11
namespace IGK\Models;
use IGK\Models\ModelBase;
/**
* auto generate doc.
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property string|datetime $clDateTime
* @method static ?self AddIfNotExists(string $clIdentifier
*/
class CookieStorages extends ModelBase{
    /**
    * Constant: fd cl id.
    * @var mixed
    */
    const FD_CL_ID="clId";
    /**
    * Constant: fd cl identifier.
    * @var mixed
    */
    const FD_CL_IDENTIFIER="clIdentifier";
    /**
    * Constant: fd cl name.
    * @var mixed
    */
    const FD_CL_NAME="clName";
    /**
    * Constant: fd cl date time.
    * @var mixed
    */
    const FD_CL_DATE_TIME="clDateTime";
	/**
	* table's name
	*/
	protected $table = "%prefix%cookie_storages";
}