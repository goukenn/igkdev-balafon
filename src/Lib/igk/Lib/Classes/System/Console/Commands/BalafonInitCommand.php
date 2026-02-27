<?php
// @author: C.A.D. BONDJE DOUE
// @file: BalafonInitCommand.php
// @date: 20231019 13:07:41
namespace IGK\System\Console\Commands;

use IGK\Constants;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\App;
use IGK\System\Console\BalafonInitEnvironment;
use IGK\System\Console\Helper\ConsoleUtility;
use IGK\System\Console\Logger;
use IGKAppSystem;
use IGKEvents; 

/**
 * 
 * @package IGK\System\Console\Commands
 */

/**
* auto generate doc.
* @package IGK\System\Console\Commands
*/
class BalafonInitCommand extends AppExecCommand
{

    /**
    * Property: command.
    * @var mixed
    */
    var $command = Constants::INIT_COMMAND;

    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = 'initiliaze environment';

    /**
    * Property: options.
    * @var mixed
    */
    var $options = [
		'--no-config' => 'flag: reset balafon.config.xml',
		'--force' => 'flag: fore re-creation',
		'--primary' => 'flag: if --no-config initialize activate the primary file generation',
		'--reset' => 'flag: use to reset application environment on --no-config',
		'--vendor-dir:[dir]' => 'composer vendor directory',
		'--app-dir:[dir]' => 'application directory',
		'--env-only' => 'flag: init environment only. disable all other flag.',
		'--file-usergroup:user-group' => 'set uxix file access group',
		'--clean'=>'flag: clean install directory',
		'--install-dir'=>'change the install diectory'
	];

    /**
    * Property: category.
    * @var mixed
    */
    var $category = 'system';

    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = 'install_dir [options]';

    /**
    * auto generate doc.
    * @param mixed $argv
    * @return void
    */

    public static function Handle(& $no_init_environment, array $argv){
		$v_env = igk_environment();
		$no_init_environment = false;
    	$v_env->NoAppInitFileStruct = false;  
		if (!in_array('--env-only', $argv)) {
			new static;
		}else{
			$no_init_environment = true;
		}
	}

    /**
    * auto generate doc.
    * @param null|string $install_dir
    * @return null
    */

    public function exec($command, ?string $install_dir = 'src')
	{
		$install_dir = empty($install_dir) ? 'src' : $install_dir;
		if (property_exists($command->options, '--env-only')) {
			Logger::info(sprintf('--[%s] - init environment only]--', IGK_CODE_NAME));
			IGKAppSystem::InitEnv($install_dir, igk_app());
			return;
		}
		$cwd = null;
		if ($cwd = igk_getv($command->options, '--install-dir')){
			if (is_dir($cwd)){
				chdir($cwd);
			}
		}
		$cwd = $cwd ?? getcwd();
		if (property_exists($command->options, '--no-config')) {
			// remove configuration 
			if (file_exists($cf = $cwd . '/' . IGK_BALAFON_CONFIG)) {
				@unlink($cf); 
			}
		}  
		igk_wln("install: ".$install_dir);
		return (new BalafonInitEnvironment())->run($command, $install_dir);
	}

    /**
    * .ctr
    */
    public function __construct()
	{
		parent::__construct();
		$this->registerHook();
	}

    /**
    * Registers Hook.
    */
    public function registerHook()
	{		
		$fc = null;
		igk_reg_hook(IGKEvents::HOOK_PREPROCESS_COMMAND_LINE, $fc = function ($e) use (& $fc) {
			igk_unreg_hook(IGKEvents::HOOK_PREPROCESS_COMMAND_LINE, $fc);
			$argv = &$e->args['argv'];
			$app = $e->args['app'];
			$l = array_search('--env-only', $argv); 
			
			if (($argv[1] == $this->command) && (false===$l)) {
	 			App::ResetCommandWorkingDir();
				$args = [];
				$targ = array_slice($argv,1);
				$command = ConsoleUtility::TreatCommandArgs($app, $targ, $args);  
				$args = array_merge([$command], $args);
				call_user_func_array([$this, 'exec'], $args);
				$e->handle = true;
				$e->output = 1;
			} 
		});
	}
}
