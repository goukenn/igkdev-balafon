<?php 
// @author: C.A.D. BONDJE DOUE
// @date: 20211220 01:45:33
namespace IGK\Controllers\IGK\Models; 
use IGK\Models\ModelBase as Model;
use IGK\Controllers\SysDbController;


/** 
 */
abstract class ModelBase extends Model {
	/**
	 * source controller 
	 */
	protected $controller = IGK\Controllers\SysDbController::class; 
}
