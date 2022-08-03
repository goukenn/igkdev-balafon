<?php 
// @author: C.A.D. BONDJE DOUE
// @date: 20220802 21:32:24
namespace IGK\Models; 
use IGK\Models\ModelBase as Model;
use IGK\System\Configuration\Controllers\UsersConfigurationController;


/** 
 */
abstract class ModelBase extends Model {
	/**
	 * source controller 
	 */
	protected $controller = \IGK\System\Configuration\Controllers\UsersConfigurationController::class; 
}
