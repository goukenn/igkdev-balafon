<?php
// @author: C.A.D. BONDJE DOUE
// @file: ListBackupCommand.php
// @date: 20260820 10:56:47
namespace IGK\System\Console\Commands\Core;

use IGK\Helper\IO;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;

/**
 * 
 * @package IGK\System\Console\Commands\Core
 * @author C.A.D. BONDJE DOUE
 */
class ListBackupCommand extends AppExecCommand
{
	var $command = '--corelib-backup';
	var $desc='view core lib backup';
	var $options=['--clean'=>'flag: remove all detected backups'];
	var $category = 'tools';
	var $usage = '[options]';
	public function exec($command)
	{
		$clean = igk_prop_exists($command->options, '--clean');
		$d = IO::GetFiles(IGK_LIB_DIR, '/\.zip$/', false);
		if ($clean){
			$this->dropAllBackup($d);
			Logger::print('clean');
			return;
		}
		if ($d) {
			foreach ($d as $f) {
				Logger::print(basename($f));
			}
		}else{
			Logger::info('no backup found');
		}
	}
	private function dropAllBackup(array $d){
		foreach($d as $f){
			@unlink($f);
		}
	}
}
