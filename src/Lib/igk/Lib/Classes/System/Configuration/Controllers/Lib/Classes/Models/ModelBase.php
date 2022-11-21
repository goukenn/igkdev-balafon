<?php 
// @author: C.A.D. BONDJE DOUE
// @date: 20221119 11:10:14
namespace IGK\Models; 
use IGK\Models\ModelBase as Model;
use IGK\System\Configuration\Controllers\ConfigureController;


/** 
 */
abstract class ModelBase extends Model {
	/**
	 * source controller 
	 */
	protected $controller = \IGK\System\Configuration\Controllers\ConfigureController::class; 
}
