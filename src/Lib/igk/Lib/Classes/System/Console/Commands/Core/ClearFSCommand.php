<?php
// @author: C.A.D. BONDJE DOUE
// @file: ClearFSCommand.php
// @date: 20261015 12:09:44
namespace IGK\System\Console\Commands\Core;

use IGK\System\Console\AppExecCommand;
use IGK\System\IO\Cache\FS;

/**
* 
* @package IGK\System\Console\Commands\Core
* @author C.A.D. BONDJE DOUE
*/
class ClearFSCommand extends AppExecCommand{
	var $command='--clearfs';
	var $desc='clear file cache';
	/* var $options=[]; */
	var $category = 'sys';
	/* var $usage = ''; */
	public function exec($command) { 

		$fs = FS::CacheFile();
		@unlink($fs);

	}
}