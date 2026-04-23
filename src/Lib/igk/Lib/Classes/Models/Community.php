<?php
// @author: C.A.D. BONDJE DOUE
// @file: Community.php
// @date: 20260102 09:35:11
namespace IGK\Models;
use IGK\Models\ModelBase;

/**
* auto generate doc.
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property string $clValueType type of data associated to value
* @method static ?self AddIfNotExists(string $clName
*/
class Community extends ModelBase{
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
    * Constant: fd cl value type.
    * @var mixed
    */
    const FD_CL_VALUE_TYPE="clValueType";
	/**
	* table's name
	*/
	protected $table = "%prefix%community";
	/**
	*override display key
	*/
	protected $display = "clName";
}