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

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command='--deploy-current';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc='deploy "current" dir script'; 
	/* var $options=[]; */

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category='script';

    /**
    * auto generate doc.
    * @param mixed $command
    * @param null|string $folder
    */
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