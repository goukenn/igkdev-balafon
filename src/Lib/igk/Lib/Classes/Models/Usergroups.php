<?php
// @author: C.A.D. BONDJE DOUE
// @file: Usergroups.php
// @date: 20240922 19:45:49
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property int|\IGK\Models\Users $clUser_Id
* @property int|\IGK\Models\Groups $clGroup_Id
* @property string|datetime $clCreate_At ="NOW()"
* @property string|datetime $clUpdate_At ="NOW()"
* @method static string FD_CL_ID() - `clId` full column name 
* @method static string FD_CL_USER_ID() - `clUser_Id` full column name 
* @method static string FD_CL_GROUP_ID() - `clGroup_Id` full column name 
* @method static string FD_CL_CREATE_AT() - `clCreate_At` full column name 
* @method static string FD_CL_UPDATE_AT() - `clUpdate_At` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(int|\IGK\Models\Users $clUser_Id, int|\IGK\Models\Groups $clGroup_Id, string|datetime $clCreate_At ="NOW()", string|datetime $clUpdate_At ="NOW()") add entry helper
* @method static ?self AddIfNotExists(int|\IGK\Models\Users $clUser_Id, int|\IGK\Models\Groups $clGroup_Id, string|datetime $clCreate_At ="NOW()", string|datetime $clUpdate_At ="NOW()") add entry if not exists. check for unique column.
* */
class Usergroups extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CL_USER_ID="clUser_Id";
	const FD_CL_GROUP_ID="clGroup_Id";
	const FD_CL_CREATE_AT="clCreate_At";
	const FD_CL_UPDATE_AT="clUpdate_At";
	/**
	* table's name
	*/
	protected $table = "%prefix%usergroups";
	protected $unique_columns = array (
	  1 => 
	  array (
	    0 => 'clUser_Id',
	    1 => 'clGroup_Id',
	  ),
	);
}