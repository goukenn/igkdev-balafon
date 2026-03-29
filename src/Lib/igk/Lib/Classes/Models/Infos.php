<?php
// @author: C.A.D. BONDJE DOUE
// @file: Infos.php
// @date: 20260102 09:35:11
namespace IGK\Models;
use IGK\Models\ModelBase;
/**
* auto generate doc.
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property string $clValue
* @method static ?self AddIfNotExists(string $clName
*/
class Infos extends ModelBase{
    /**
    * Constant: fd cl id.
    * @var mixed
    */
    const FD_CL_ID="clId";
    /**
    * Constant: fd cl name.
    * @var mixed
    */
    const FD_CL_NAME="clName";
    /**
    * Constant: fd cl value.
    * @var mixed
    */
    const FD_CL_VALUE="clValue";
	/**
	* table's name
	*/
	protected $table = "%prefix%infos";
}