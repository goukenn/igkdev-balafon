<?php
// @author: C.A.D. BONDJE DOUE
// @file: ListBackupCommand.php
// @date: 20260820 10:56:47
namespace IGK\System\Console\Commands\Core;

use IGK\Helper\IO;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
/**
* auto generate doc.
* @package IGK\System\Console\Commands\Core
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\Console\Commands\Core
*/
class ListBackupCommand extends AppExecCommand
{
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    var $command = '--corelib-backup';
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    var $desc='view core lib backup';
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    var $options=['--clean'=>'flag: remove all detected backups'];
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    var $category = 'tools';
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    var $usage = '[options]';
    /**
    * auto generate doc.
    * @param mixed $command
    * @return void
    */
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
    /**
    * auto generate doc.
    * @param array $d
    * @return void
    */
    private function dropAllBackup(array $d){
		foreach($d as $f){
			@unlink($f);
		}
	}
}
