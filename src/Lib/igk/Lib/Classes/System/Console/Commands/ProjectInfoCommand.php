<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectInfoCommand.php
// @date: 20230313 21:45:12
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Configuration\ProjectInfo;
use IGK\System\Configuration\ProjectConfigInfo;
use IGK\Helper\Activator;
///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands
*/
class ProjectInfoCommand extends AppExecCommand{
	var $command='--project:info';
	var $desc='view project\'s store info '; 
	var $options=[];
	var $category = "project";
	public function exec($command, string $controller = null) {
		$ctrl = ($controller ? self::GetController($controller) : null)?? die("missing controller");
		
		$dir = $ctrl->getDeclaredDir();

		$inf = new ProjectInfo;
		$inf->base_dir = $dir;
		$inf->name = $ctrl->getName();
		$inf->settings = $ctrl->getConfigs()->to_array();
		$inf->type = "project";
		if (is_file($f = $dir."/balafon.config.json")){
			$inf->configs = Activator::CreateNewInstance(ProjectConfigInfo::class , 
				 json_decode(file_get_contents($f)));
		}
		if (is_file($f = $dir."/package.json")){
			$inf->package_json = Activator::CreateNewInstance(ProjectConfigInfo::class , 
				 json_decode(file_get_contents($f)));
		}

		echo json_encode($inf, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
		echo PHP_EOL;
	 }
}