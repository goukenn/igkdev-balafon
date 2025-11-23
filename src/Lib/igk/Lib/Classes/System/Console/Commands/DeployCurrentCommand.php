<?php
// @author: C.A.D. BONDJE DOUE
// @file: DeployCurrentCommand.php
// @date: 20230705 09:57:39
namespace IGK\System\Console\Commands;

use IGK\Helper\IO;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
/**
* 
* @package IGK\System\Console\Commands
*/
class DeployCurrentCommand extends AppExecCommand{
	var $command='--deploy-current';
	 var $desc='deploy "current" dir script'; 
	/* var $options=[]; */
	var $category='script'; 
	public function exec($command, ?string $folder = null) {
		$folder || igk_die('require folder');
		if (is_link('current'))
        	@unlink('current');
		$dir = $folder;
		IO::CreateDir($dir);
		// + | target , file
		symlink($dir, 'current');
		Logger::success('done. current => '.realpath($dir));
	}
}