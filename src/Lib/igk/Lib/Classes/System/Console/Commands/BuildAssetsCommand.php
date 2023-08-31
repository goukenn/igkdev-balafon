<?php
// @author: C.A.D. BONDJE DOUE
// @file: BuildAssetsCommand.php
// @date: 20230719 14:12:35
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGKEvents;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands
*/
class BuildAssetsCommand extends AppExecCommand{
	var $command='--build:assets';
	var $category='build';
	var $desc='invoke asset builder - raise Build_asset event';
	/* var $options=[]; */
	/* var $category; */
	public function exec($command) {
		Logger::print('Build assets');		
		$v_projects = igk_sys_get_projects_controllers() ?? [];
		$bdir = igk_io_basedir();
		Logger::info('build project');
		self::BuildAssets($v_projects);
		Logger::info('build modules');
		$v_modules = array_map(function($p){			 
			return  igk_get_module($p->name);
		}, igk_get_modules());
		self::BuildAssets(array_filter($v_modules)); 
		igk_hook(IGKEvents::BUILD_ASSETS, ['cmd'=>'console']);		
		Logger::success('done');  
	}
	static function BuildAssets($modules, ?string $bdir = null){
		$bdir = $bdir ?? igk_io_basedir();
		foreach($modules as $ctrl){			
			$ctrl->register_autoload();
			if ($v_cl = $ctrl->resolveClass(\System\AssetBuilder::class)){
				Logger::info($ctrl->getName().' assets ....');
				$v_ocl = new $v_cl();
				$v_ocl->build($ctrl, $bdir);
			}
		} 
	}
}