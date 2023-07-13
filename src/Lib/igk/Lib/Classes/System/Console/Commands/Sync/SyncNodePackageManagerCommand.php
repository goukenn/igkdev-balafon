<?php
// @author: C.A.D. BONDJE DOUE
// @file: SyncNodePackageManagerCommand.php
// @date: 20230629 15:30:45
namespace IGK\System\Console\Commands\Sync;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Commands\SyncAppExecCommandBase\Sync;
use IGK\System\Console\Logger;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands\Sync
*/
class SyncNodePackageManagerCommand extends SyncAppExecCommandBase{
	var $command='--sync:node-package-manager';
	var $desc='sync node package manager';

	 /**
     * get merged scripts
     * @return string[] 
     */
    protected function getMergedScripts(){
        return [
            IGK_LIB_DIR."/Inc/core/installer-helper.pinc",
			IGK_LIB_CLASSES_DIR."/System/Shell/OsShell.php",
            IGK_LIB_DIR."/Inc/core/sync-node-package.pinc",   
        ];
    }

	 
	/* var $options=[]; */
	/* var $category; */
	public function exec($command, ...$args) { 
		$this->syncScriptCommand($command, 'sync-npm-package.php', ['args'=>$args]);	
        Logger::success(":complete");

	}
}