<?php 
// @author: C.A.D. BONDJE DOUE
// @date: 20221119 11:10:14
namespace IGK\Models;

use IGK\Controllers\SysDbController;
use IGK\Models\ModelBase as Model; 


/** 
 */
abstract class ModelBase extends Model {
	/**
	 * source controller 
	 */
	protected $controller = SysDbController::class; 
}
