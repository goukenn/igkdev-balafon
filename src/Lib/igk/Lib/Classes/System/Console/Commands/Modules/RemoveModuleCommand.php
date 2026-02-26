<?php
// @author: C.A.D. BONDJE DOUE
// @file: RemoveModuleCommand.php
// @date: 20251016 20:11:19
namespace IGK\System\Console\Commands\Modules;

use IGK\Helper\IO;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Controllers\ApplicationModules;

/**
 * 
 * @package IGK\System\Console\Commands\Modules
 * @author C.A.D. BONDJE DOUE
 */
class RemoveModuleCommand extends AppExecCommand
{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command = '--module:remove';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc = 'remove installed module';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $options = [];

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category = 'module';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $usage = '[options]';

    /**
    * auto generate doc.
    * @param mixed $command
    * @param null|string $module_name
    */
    public function exec($command, ?string $module_name = null)
	{
		!$module_name && igk_die('module required');
		try {
			if (!($v_cmod = igk_get_module($module_name))) {
				igk_die("module not found");
			}

			$dir = $v_cmod->getDeclaredDir();

			Logger::info("module:". $v_cmod->getName()); 
			$rm = true;
			if (function_exists('readline')) {
				if ('y' == readline('confirm module suppression ? (y|n)')) {
					IO::RmDir($dir);
				} else{
					$rm = false;
				}
			}
			if ($rm){
				@unlink(ApplicationModules::GetCacheFile());
			}
		} catch (\Exception $ex) {
			Logger::danger($ex->getMessage());
		}
		Logger::success('done');
		return 0;
	}
}
