<?php
// @author: C.A.D. BONDJE DOUE
// @file: Subdomains.php
// @date: 20231219 13:50:52
namespace IGK\Models;


use IGK\Models\ModelBase;

///<summary>store sub domain</summary>
/**
* store sub domain
* @package IGK\Models
* @property int $clId
* @property string $clName Subdomain name. exemple. 'mail'  in the .domain.com will be mail.domain.dom
* @property string $clCtrl Controller name
* @property string $clView Entry
* @property string|datetime $clDeactivate_At
* @property string|datetime $clCreate_At ="Now()"
* @property string|datetime $clUpdate_At ="Now()"
* @method static ?self Add(string $clName, string $clCtrl, string $clView, string|datetime $clDeactivate_At, string|datetime $clCreate_At ="Now()", string|datetime $clUpdate_At ="Now()") add entry helper
* @method static ?self AddIfNotExists(string $clName, string $clCtrl, string $clView, string|datetime $clDeactivate_At, string|datetime $clCreate_At ="Now()", string|datetime $clUpdate_At ="Now()") add entry if not exists. check for unique column.
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
}