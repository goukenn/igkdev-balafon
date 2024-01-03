<?php
// @author: C.A.D. BONDJE DOUE
// @file: Groups.php
// @date: 20240101 17:47:26
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>Store framework groups</summary>
/**
* Store framework groups
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $clName
* @property string $clDescription
* @property string $clController
* @property string|datetime $clCreate_At ="NOW()"
* @property string|datetime $clUpdate_At ="NOW()"
* @method static ?self Add(string $clName, string $clDescription, string $clController, string|datetime $clCreate_At ="NOW()", string|datetime $clUpdate_At ="NOW()") add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clDescription, string $clController, string|datetime $clCreate_At ="NOW()", string|datetime $clUpdate_At ="NOW()") add entry if not exists. check for unique column.
* */
class Groups extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CL_NAME="clName";
	const FD_CL_DESCRIPTION="clDescription";
	const FD_CL_CONTROLLER="clController";
	const FD_CL_CREATE_AT="clCreate_At";
	const FD_CL_UPDATE_AT="clUpdate_At";
	/**
	* table's name
	*/
	protected $table = "%prefix%groups"; 
	protected $unique_columns = array (
	  0 => 
	  array (
	    0 => 'clName',
	    1 => 'clController',
	  ),
	);
}