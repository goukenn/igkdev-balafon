<?php
// @author: C.A.D. BONDJE DOUE
// @file: ImportDataFileCommand.php
// @date: 20240918 16:42:02
namespace IGK\System\Console\Commands\Database;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Database\Import\DbImportFile;

/**
* auto generate doc.
* @package IGK\\System\Console\Commands\Database
* @author C.A.D. BONDJE DOUE
*/
class ImportDataFileCommand extends AppExecCommand{
    /**
    * Property: command.
    * @var mixed
    */
    var $command='--db:import';
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc='import data from description file';
    /**
    * Property: options.
    * @var mixed
    */
    var $options=[
		"-f:file"=>"file to import",
		"--entry:"=>"set entry definition",
		"--autoregister"=>"flag: autore register unknow entries"
	];
    /**
    * Property: category.
    * @var mixed
    */
    var $category="db";
    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = "([controller] model [options]";
    /**
    * Exec.
    * @param mixed $command
    * @param null|string $controller
    * @param null|string $model
    */
    public function exec($command, ?string $controller=null, ?string $model=null) { 
		if (empty($model)){
			if (!empty($controller)){
				$model = $controller;
				$controller = null;
			}
		}
		$ctrl = self::ResolveController($command, $controller, false) ?? igk_die('controller required');
		empty($model) && igk_die('missing model');
		$file = igk_getv($command->options, '-f') ?? igk_die('missing file');
		$type = igk_getv($command->options, '-t');
		$autoregister = property_exists($command->options, '--autoregister');
		$entry = igk_getv($command->options, '--entry');
		self::BindUserCommand($ctrl, $command);
		$model = $ctrl->model($model);
		if ($model){
			Logger::info('importing...');
			DbImportFile::Import($model, $file, $type, $autoregister, $entry);
			Logger::success('done');
		}
		else{
			Logger::danger('missing model');
		}		
		return 0;
	}
}