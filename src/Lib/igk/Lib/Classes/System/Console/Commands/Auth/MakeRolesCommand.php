<?php
// @author: C.A.D. BONDJE DOUE
// @file: MakeRolesCommand.php
// @date: 20250210 15:43:21
namespace IGK\System\Console\Commands\Auth;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\System\IO\Path;
/**
* auto generate doc.
* @package IGK\System\Console\Commands\Auth
* @author C.A.D. BONDJE DOUE
*/
class MakeRolesCommand extends AppExecCommand{
    /**
    * Property: command.
    * @var mixed
    */
    var $command='--make:role';
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc='create a role definition file '; 
	/* var $options=[]; */
	/* var $category = ''; */
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
		$ctrl = self::GetController($controller);
		$path = $ctrl->getConfigsDir();
		$l = Path::Combine($path, 'profiles.php');
		if (igk_io_file_exists($l)){
			Logger::info('file already exists : '.$l);
			return -2;
		}
		$s = new PHPScriptBuilder;
		$s->desc('profiles definition')
		->type('function')
		->defs('return [];');
		$u = igk_io_w2file($l, $s->render());
		Logger::success($u ? 'done': 'failed');
		if (!$u)
			return -1;
		Logger::success("output: ".$l);
	}	
}