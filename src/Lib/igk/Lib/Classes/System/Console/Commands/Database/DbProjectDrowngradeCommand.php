<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbProjectDrowngradeCommand.php
// @date: 20231222 12:01:46
namespace IGK\System\Console\Commands\Database;
use IGK\Database\DbSchemas;
use IGK\Database\DbSchemasConstants;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Commands\DbCommandHelper;
use IGK\System\Console\Logger;
use IGK\System\Database\MigrationHandler;
use IGK\System\Database\SchemaMigrationInfo;

/**
* auto generate doc.
* @package IGK\System\Console\Commands\Database
*/
class DbProjectDrowngradeCommand extends AppExecCommand{
    /**
    * Property: command.
    * @var mixed
    */
    var $command='--db:downgrade';
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc='downgrade project database';
	/* var $options=[]; */
    /**
    * Property: category.
    * @var mixed
    */
    var $category = 'sys:db';
    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = 'controller [options]';
    /**
    * Exec.
    * @param mixed $command
    * @param null|string $controller
    */
    public function exec($command, ?string $controller=null) {
		$ctrl = self::GetController($controller);
		DbCommandHelper::Init($command);
		Logger::info('downgrade .... '.$ctrl->getName());
		$schama_file = $ctrl->getDataSchemaFile(); 
		$info = DbSchemas::LoadSchema($schama_file, $ctrl, true, DbSchemasConstants::Migrate);
		DbSchemas::InitData($ctrl, $info, $ctrl->getDataAdapter());
		if (($s = $info->tables['delete'] ) instanceof SchemaMigrationInfo){
		}
		$migHandle = new MigrationHandler($ctrl);
        $migHandle->up();
		Logger::success('done');
	}
}