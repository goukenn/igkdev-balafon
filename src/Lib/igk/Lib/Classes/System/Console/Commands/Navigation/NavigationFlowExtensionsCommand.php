<?php
// @author: C.A.D. BONDJE DOUE
// @file: NavigationFlowExtensionsCommand.php
// @date: 20260212 16:30:17
namespace IGK\System\Console\Commands\Navigation;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\FileHandler;

/**
 * 
 * @package IGK\System\Console\Commands\Navigation
 * @author C.A.D. BONDJE DOUE
 */
class NavigationFlowExtensionsCommand extends AppExecCommand
{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command = '--navigation-flow-extensions';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc = 'list navigation flow file extensions';
	/* var $options=[]; */

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category = 'sys';
	/* var $usage = ''; */

    /**
    * auto generate doc.
    * @param mixed $command
    */
    public function exec($command)
	{

		$cp = ['.' . IGK_DEFAULT_VIEW_EXT];
		$dp = FileHandler::GetContextFileHandlers(FileHandler::FILE_CONTEXT_VIEW);
		if ($dp) {
			$cp = array_merge($cp, array_keys($dp));
		}

		echo json_encode($cp, JSON_PRETTY_PRINT), PHP_EOL;

		Logger::success('done');
	}
}
