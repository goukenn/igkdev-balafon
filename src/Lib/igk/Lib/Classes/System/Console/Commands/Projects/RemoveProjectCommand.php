<?php
// @author: C.A.D. BONDJE DOUE
// @file: RemoveProjectCommand.php
// @date: 20231223 15:49:13
namespace IGK\System\Console\Commands\Projects;
use IGK\Controllers\ControllerExtension;
use IGK\Controllers\SysDbController;
use IGK\Helper\IO;
use IGK\Helper\SysUtils;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Commands\DbCommandHelper;
use IGK\System\Console\Logger;
use IGK\System\Database\MigrationHandler;
use IGKEvents;

/**
* auto generate doc.
* @package IGK\System\Console\Commands\Projects
*/
class RemoveProjectCommand extends AppExecCommand{
    /**
    * Property: command.
    * @var mixed
    */
    var $command='--project:remove';
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc='remove install project';
	/* var $options=[]; */
    /**
    * Property: category.
    * @var mixed
    */
    var $category = 'project';
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
    public function exec($command, ?string $controller = null) { 
		$ctrl = self::GetController($controller) ?? igk_die('missing controller');
		DbCommandHelper::Init($command);
		$sm = new RemoveProjectMiddleWare;
		$mig = new MigrationHandler($ctrl);
		$mig->down(false); 
		Logger::info('drop used datbase');
		ControllerExtension::dropDb($ctrl, false, true); 
		Logger::info('move project to .removed project folder');
		IO::CreateDir($dir = IGK_PROJECT_DIR.'/.removed');
		$v_dec = $ctrl->getDeclaredDir();
		$v_folder = basename($v_dec); 
		rename($v_dec, $new_dir =  $dir.'/'.$v_folder);
		Logger::info('clear cache');
        igk_hook(IGKEvents::HOOK_PROJECT_REMOVED, ['ctrl'=>$ctrl, 'new_dir'=>$new_dir]);
		SysUtils::ClearCache();
	}	
}