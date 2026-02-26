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
* 
* @package IGK\System\Console\Commands\Database
*/
class DbProjectDrowngradeCommand extends AppExecCommand{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command='--db:downgrade';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc='downgrade project database';
	/* var $options=[]; */

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category = 'sys:db';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $usage = 'controller [options]';

    /**
    * auto generate doc.
    * @param mixed $command
    * @param null|string $controller
    */
    public function exec($command, ?string $controller=null) {
		// get controller schema
		$ctrl = self::GetController($controller);
		DbCommandHelper::Init($command);
		Logger::info('downgrade .... '.$ctrl->getName());
		//$ctrl::initDb();
		$schama_file = $ctrl->getDataSchemaFile(); 
		$info = DbSchemas::LoadSchema($schama_file, $ctrl, true, DbSchemasConstants::Migrate);
		DbSchemas::InitData($ctrl, $info, $ctrl->getDataAdapter());
		if (($s = $info->tables['delete'] ) instanceof SchemaMigrationInfo){
		}
		// run controller migration.... 
		$migHandle = new MigrationHandler($ctrl);
        $migHandle->up();
		Logger::success('done');
	}
}