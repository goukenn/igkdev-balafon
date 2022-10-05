<?php 
// @author: C.A.D. BONDJE DOUE
// @date: 20220915 17:51:19
namespace IGK\Models; 
use IGK\Models\ModelBase as Model;
use IGK\System\Database\MySQL\Controllers\DbConfigController;


/** 
 */
abstract class ModelBase extends Model {
	/**
	 * source controller 
	 */
	protected $controller = \IGK\System\Database\MySQL\Controllers\DbConfigController::class; 
}
