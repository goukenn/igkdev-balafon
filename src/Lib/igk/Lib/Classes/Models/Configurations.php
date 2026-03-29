<?php
// @author: C.A.D. BONDJE DOUE
// @file: Configurations.php
// @date: 20260102 09:35:11
namespace IGK\Models;
use IGK\Models\ModelBase;
/**
* auto generate doc.
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property string $clDescription
* @method static ?self AddIfNotExists(string $clName
*/
class Configurations extends ModelBase{
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
    * Constant: fd cl description.
    * @var mixed
    */
    const FD_CL_DESCRIPTION="clDescription";
	/**
	* table's name
	*/
	protected $table = "%prefix%configurations";
	/**
	*override display key
	*/
	protected $display = "clName";
}