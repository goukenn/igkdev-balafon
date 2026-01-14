<?php
// @author: C.A.D. BONDJE DOUE
// @file: BalafonInitCommand.php
// @date: 20231019 13:07:41
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\BalafonInitEnvironment;
use IGK\System\Console\Helper\ConsoleUtility;
use IGK\System\Console\Logger;
use IGKAppSystem;
use IGKEvents;

 //igk_wln(__FILE__.':'.__LINE__, 'loading ... initialize');

/**
 * 
 * @package IGK\System\Console\Commands
 */
class BalafonInitCommand extends AppExecCommand
{
	var $command = '--init';
	var $desc = 'initiliaze environment';
	var $options = [
		'--noconfig' => 'flag: enabled ',
		'--force' => 'flag: fore re-creation',
		'--primary' => 'flag: if --noconfig initialize activate the primary file generation',
		'--reset' => 'flag: use to reset application environment on --noconfig',
		'--vendor-dir:[dir]' => 'composer vendor directory',
		'--app-dir:[dir]' => 'application directory',
		'--env-only' => 'flag: init environment only. disable all other flag.',
		'--file-usergroup:user-group' => 'set uxix file access group',
		'--clean'=>'flag: clean install directory'
	];
	var $category = 'system';
	var $usage = 'install_dir [options]';
	/**
	 * 
	 * @param IExecCommand $command 
	 * @param null|string $install_dir 
	 * @return null 
	 */
	public function exec($command, ?string $install_dir = 'src')
	{
		$install_dir = empty($install_dir) ? 'src' : $install_dir;
		if (property_exists($command->options, '--env-only')) {
			Logger::info('init environment only');
			IGKAppSystem::InitEnv($install_dir, igk_app(), [
				'user-group'=>igk_getv($command->options, '--file-usergroup', 'www-data:www-data')
			]);
			return;
		}

		 

		return (new BalafonInitEnvironment())->run($command, $install_dir);
	}
	public function __construct()
	{
		parent::__construct();
		$this->registerHook();
	}
	public function registerHook()
	{
		// igk_ilog('register .... hook');
		$fc = null;
		igk_reg_hook(IGKEvents::HOOK_PREPROCESS_COMMAND_LINE, $fc = function ($e) use (& $fc) {
			igk_unreg_hook(IGKEvents::HOOK_PREPROCESS_COMMAND_LINE, $fc);
			$argv = &$e->args['argv'];
			$app = $e->args['app'];
			$l = array_search('--env-only', $argv);
			if (($argv[1] == $this->command) && (false===$l)) {
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
