<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectBuildCommand.php
// @date: 20230303 13:56:48
namespace IGK\System\Commmands;

use IGK\Controllers\SysDbController;
use IGK\Helper\Activator;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\TamTam\ProjectBuilderEvents;

use function igk_resource_gets as __;

///<summary></summary>
/**
* 
* @package IGK\System\Commmands
*/
class ProjectBuildCommand extends AppExecCommand{
	var $command='--project:build';
	var $desc='build project for production'; 
	var $options=[]; 
	public function showUsage(){
		Logger::print($this->command." controller [options]");
	}
	/* var \$category; */
	public function exec($command, ?string $controller =null ) { 
		$ctrl = $this->_dieController($command, $controller);
		$project_builder_cl = igk_configs()->get('ProjectBuilder', \IGK\System\TamTam\ProjectBuilder::class) ?? igk_die("require a global project builder");
		$project_after_build_options_cl = igk_configs()->get('ProjectBuilder', \IGK\System\TamTam\ProjectBuilderAfterBuildHookOption::class) ?? igk_die("require a global project builder");
		$project_builder = new $project_builder_cl();
		$args = ["type"=>"project", "ctrl"=>$ctrl, "builder"=>$project_builder];
		$options = (object)['cancel'=>false];

		Logger::info("Build [".$ctrl->getName()."] for production...\n");

		Logger::info('Before build...');
		$o = igk_hook(ProjectBuilderEvents::BEFORE_BUILD, $args, $options);
		if ($o && isset($o->cancel)){
			igk_die("before build canceled");
		}

		Logger::info('Build...');
		$ctrl->exposeAssets();
		igk_hook(ProjectBuilderEvents::BUILD, $args);

		$options = Activator::CreateNewInstance($project_after_build_options_cl, (object)['errors'=>[],
					'output'=>null,
					'args'=>$args
				]);

		Logger::info('after build...');
		$o = igk_hook(ProjectBuilderEvents::AFTER_BUILD, $args, $options);
		if ($o && $o->errors){
			Logger::danger('there are some errors ');
		}else
			Logger::success("build complete successfully", $o);
	}	
}