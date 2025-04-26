<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbInitModuleDatabaseCommand.php
// @date: 20250424 21:47:36
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGKModuleListMigration;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands
* @author C.A.D. BONDJE DOUE
*/
class DbInitModuleDatabaseCommand extends AppExecCommand{
	var $command='--db:module-initdb';
	/* var $desc='desc'; */
	var $options=[ '--force'=>'flag: force init db'];
	var $category = 'module';
	var $usage = 'module_id [option]'; 
	public function exec($command, ?string $module_name = null) { 
		$module_name || igk_die('required module name');
		($mod = igk_get_module($module_name)) || igk_die('module not found');
		$force = property_exists($command->options, '--force');
		IGKModuleListMigration::InitDbModule($force, [$mod]);
	}
}