<?php
// @author: C.A.D. BONDJE DOUE
// @file: BalafonInitCommand.php
// @date: 20231019 13:07:41
namespace IGK\System\Console\Commands;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\BalafonInitEnvironment;
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
		'--reset'=>'flag: use to reset application environment on --noconfig'
	]; 
	var $category='system';
	var $usage = 'install_dir [options]'; 
	public function exec($command, ?string $install_dir='src') {
		$install_dir = empty($install_dir) ? 'src' : $install_dir;
		return (new BalafonInitEnvironment())->run($command, $install_dir); 
	 }
}