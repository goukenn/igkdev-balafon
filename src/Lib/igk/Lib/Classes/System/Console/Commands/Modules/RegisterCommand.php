<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegisterCommand.php
// @date: 20260205 12:52:03
namespace IGK\System\Console\Commands\Modules;

use IGK\System\Console\AppExecCommand;

/**
* 
* @package IGK\System\Console\Commands\Modules
* @author C.A.D. BONDJE DOUE
*/
class RegisterCommand extends AppExecCommand{
	var $command='--module:register';
	var $desc='register module to online Balafon\'s module package repository'; 
	var $options=[];
	var $category = 'module';
	var $usage = '[options]';
	public function exec($command, ?string $module_name=null) { 
		//TODO;
		$mod = ($module_name ? igk_get_module($module_name) ?? igk_die('missing module'): null) ?? igk_current_module() ?? igk_die('module not found');

		igk_wln_e($mod);
	}
}