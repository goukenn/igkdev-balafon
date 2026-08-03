<?php
// @author: C.A.D. BONDJE DOUE
// @file: InfoCommand.php
// @date: 20260401 10:27:28
namespace IGK\System\Console\Commands;
use IGK\System\Console\AppExecCommand;
use IGK\System\IO\Path;
use IGKEvents;

/**
* show balafon'on basics informations
* @package IGK\System\Console\Commands
*/
class InfoCommand extends AppExecCommand{
	var $command='--info';
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    var $desc='retrieve essential balafon information';
	/* var $options=[]; */
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    var $category = 'sys';
	/* var $usage = ''; */
    /**
    * auto generate doc.
    * @param mixed $command
    * @return void
    */
    public function exec($command) { 
		$obj = igk_createobj();
		$obj->version = IGK_VERSION;
		$obj->cwd = getcwd();
		$obj->libdir = IGK_LIB_DIR;
		$cp =  Path::getInstance();
        $obj->cachedir = igk_io_cachedir();
		$obj->homedir = $cp->getHomeDir();
		$obj->moduledir = igk_get_module_dir();
		$obj->packagedir = igk_io_packagesdir();
		$obj->projectdir = igk_io_projectdir();
		igk_hook(IGKEvents::FILTER_BALAFON_COMMAND_INFO, ['info'=>$obj]);
		echo json_encode($obj, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		echo "\n";
	}
}