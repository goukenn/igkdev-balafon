<?php
// @author: C.A.D. BONDJE DOUE
// @file: SyncConfigCommand.php
// @date: 20231020 17:12:40
namespace IGK\System\Console\Commands\Sync;

use IGK\System\Console\Logger;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands\Sync
*/
class SyncConfigCommand extends SyncAppExecCommandBase{
	var $command='--sync:config';
	var $desc='show ftp-sync configuration';   
	public function exec($command) {
		$cf = $command->app->getConfigs(); 
		$rf = $cf->get('ftp-sync');
		if (!$rf){
			Logger::danger("missing ftp-sync configuration file");
			return -1;
		}
		$d = $rf;
		echo json_encode($d, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		
	}
}