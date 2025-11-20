<?php
// @author: C.A.D. BONDJE DOUE
// @file: BalafonInitCommand.php
// @date: 20231019 13:07:41
namespace IGK\System\Console\Commands;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\BalafonInitEnvironment;
use IGK\System\Console\Logger;
use IGKAppSystem;

/**
* 
* @package IGK\System\Console\Commands
*/
class BalafonInitCommand extends AppExecCommand{
	var $command='--init';
	var $desc='initiliaze environment'; 
	var $options=[
		'--noconfig'=>'flag: enabled ',
		'--force'=>'flag: fore re-creation', 
		'--primary'=>'flag: if --noconfig initialize activate the primary file generation',
		'--reset'=>'flag: use to reset application environment on --noconfig',
		'--vendor-dir:[dir]'=>'composer vendor directory',
		'--app-dir:[dir]'=>'application directory',
		'--env-only'=>'flag: init environment only. disable all other flag.'
	]; 
	var $category='system';
	var $usage = 'install_dir [options]'; 
	public function exec($command, ?string $install_dir='src') {
		$install_dir = empty($install_dir) ? 'src' : $install_dir;
		if (property_exists($command->options, '--env-only')){
			Logger::info('init environment only');
			IGKAppSystem::InitEnv($install_dir, igk_app());
			return;
		}
		return (new BalafonInitEnvironment())->run($command, $install_dir); 
	}
}