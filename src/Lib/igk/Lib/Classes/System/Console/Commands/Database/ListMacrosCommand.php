<?php
// @author: C.A.D. BONDJE DOUE
// @file: ListMacrosCommand.php
// @date: 20240104 08:40:30
namespace IGK\System\Console\Commands\Database;

use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands\Database
* @author C.A.D. BONDJE DOUE
*/
class ListMacrosCommand extends AppExecCommand{
	var $command='--db:macros';
	var $desc='list controller macros';
	/* var $options=[]; */
	var $category = 'db';
	public function exec($command, ?string $controller=null, ?string $ModelName=null) {
		$ctrl = $controller ? self::GetController($controller) : SysDbController::ctrl();
		if ($ctrl instanceof BaseController){
		if ($ModelName){
			$model = $ctrl->model($ModelName);

			$macros = $model->getMacroKeys();
			Logger::print(sprintf('List [%s] model macros', $ctrl->getName()));
			foreach($macros as $m){
				Logger::print($m);
			}

		}else {
			Logger::print('List all models and macros');
				
			$tabl = $ctrl::getModels();

		}
		
	}
		Logger::success('done');
	}
}