<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbInitModuleDatabaseCommand.php
// @date: 20250424 21:47:36
namespace IGK\System\Console\Commands;
use IGK\System\Console\AppExecCommand;
use IGKModuleListMigration;
/**
* 
* @package IGK\System\Console\Commands
* @author C.A.D. BONDJE DOUE
*/
class DbInitModuleDatabaseCommand extends AppExecCommand{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command='--module:initdb';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc='init module database';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $options=[ '--force'=>'flag: force init db'];

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category = 'module';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $usage = 'module_id [option]';

    /**
    * auto generate doc.
    * @param mixed $command
    * @param null|string $module_name
    */
    public function exec($command, ?string $module_name = null) { 
		$module_name || igk_die('required module name');
		($mod = igk_get_module($module_name)) || igk_die('module not found');
		$force = property_exists($command->options, '--force');
		IGKModuleListMigration::InitDbModule($force, [$mod]);
	}
}