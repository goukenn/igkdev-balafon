<?php
// @author: C.A.D. BONDJE DOUE
// @file: InfoCommand.php
// @date: 20260401 10:27:28
namespace IGK\System\Console\Commands;
use IGK\System\Console\AppExecCommand;
use IGK\System\IO\Path;
use IGKEvents;

/**
* 
* @package IGK\System\Console\Commands
* @author C.A.D. BONDJE DOUE
*/
class InfoCommand extends AppExecCommand{
	var $command='--info';
	var $desc='retrieve essential balafon information';
	/* var $options=[]; */
	var $category = 'sys';
	/* var $usage = ''; */
	public function exec($command) { 
		$obj = igk_createobj();
		$obj->version = IGK_VERSION;
		$obj->cwd = getcwd();
		$obj->libdir = IGK_LIB_DIR;
		$cp =  Path::getInstance();
		$obj->homedir = $cp->getHomeDir();
		$obj->packagedir = igk_io_packagesdir();
		$obj->moduledir = igk_get_module_dir();
		$obj->projectdir = igk_io_projectdir();
		igk_hook(IGKEvents::FILTER_BALAFON_COMMAND_INFO, ['info'=>$obj]);
		echo json_encode($obj, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		echo "\n";
	}
}