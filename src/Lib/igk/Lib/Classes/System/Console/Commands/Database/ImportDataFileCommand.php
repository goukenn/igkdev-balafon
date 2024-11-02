<?php
// @author: C.A.D. BONDJE DOUE
// @file: ImportDataFileCommand.php
// @date: 20240918 16:42:02
namespace IGK\System\Console\Commands\Database;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Database\Import\DbImportFile;

///<summary></summary>
/**
* 
* @package IGK\\System\Console\Commands\Database
* @author C.A.D. BONDJE DOUE
*/
class ImportDataFileCommand extends AppExecCommand{
	var $command='--db:import';
	var $desc='import data desc';
	var $options=[
		"-f:file"=>"file to import",
		// "-t:type"=>"force file as type json|csv"
	];
	var $category="db";
	var $usage = "([controller] model [options]";
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
		$model = $ctrl->model($model);
		if ($model){
			DbImportFile::Import($model, $file, $type, $autoregister);
			Logger::success('done');
		}
		else{
			Logger::danger('missing model');
		}		
		return 0;

	}
}