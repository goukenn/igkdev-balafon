<?php
// @author: C.A.D. BONDJE DOUE
// @file: Subdomains.php
// @date: 20260821 14:07:37
namespace IGK\Models;


use IGK\Models\ModelBase;

/**
* store sub domain
* @package IGK\Models
* @author C.A.D. BONDJE DOUE
* @property int $clId
* @property string $clName Subdomain name. exemple. 'mail'  in the .domain.com will be mail.domain.dom
* @property string $clCtrl Controller name
* @property string $clView Entry
* @property string $clDeactivate_At
* @property string $clCreate_At ="Now()"
* @property string $clUpdate_At ="Now()"
* @method static string FN_CL_ID() - `clId` full column name 
* @method static string FN_CL_NAME() - `clName` full column name 
* @method static string FN_CL_CTRL() - `clCtrl` full column name 
* @method static string FN_CL_VIEW() - `clView` full column name 
* @method static string FN_CL_DEACTIVATE_AT() - `clDeactivate_At` full column name 
* @method static string FN_CL_CREATE_AT() - `clCreate_At` full column name 
* @method static string FN_CL_UPDATE_AT() - `clUpdate_At` full column name 
* @method static ?array joinOnClid($call=null, ?string $type=null, string $op=\IGK\System\Database\JoinTableOp::EQUAL) - macros function 
* @method static ?string targetOnClid() - macros function
* @method static ?self Add(string $clName, string $clCtrl, string $clView, string $clDeactivate_At, string $clCreate_At ="Now()", string $clUpdate_At ="Now()") add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clCtrl, string $clView, string $clDeactivate_At, string $clCreate_At ="Now()", string $clUpdate_At ="Now()") add entry if not exists. check for unique column.
* @method static mixed GetAllActivateDomain() macros function definition 
* @method static mixed RegisterSubDomain(string $domain,\IGK\Controllers\BaseController $controller,?string $view= null) macros function definition
* */
class Subdomains extends ModelBase{
	const FD_CL_ID="clId";
	const FD_CL_NAME="clName";
	const FD_CL_CTRL="clCtrl";
	const FD_CL_VIEW="clView";
	const FD_CL_DEACTIVATE_AT="clDeactivate_At";
	const FD_CL_CREATE_AT="clCreate_At";
	const FD_CL_UPDATE_AT="clUpdate_At";
	/**
	* table's name
	*/
	protected $table = "%prefix%subdomains";
	/**
	*override display key
	*/
	protected $display = "clName";
}