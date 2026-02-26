<?php
// @author: C.A.D. BONDJE DOUE
// @file: ComposerCommand.php
// @date: 20230311 09:12:52
namespace IGK\System\Console\Commands;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Shell\OsShell;
/**
* 
* @package IGK\System\Console\Commands
*/
class ComposerCommand extends AppExecCommand{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command='--composer';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc='help manage balafon composer packages';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $options=[];

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category = 'composer';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $usage = 'composer_args [options]';

    /**
    * auto generate doc.
    * @param mixed $command
    * @param string ...$args
    */
    public function exec($command, string ...$args) { 
		$packages = igk_io_packagesdir();
		if (empty($composer = OsShell::where("composer.phar"))){
			$composer = $packages."/composer.phar";
		}
		if (!$composer || !is_file($composer))
		{
			Logger::danger("missing composer.phar");
			return -1;
		}
		// if (empty($args))
		// {
		// 	Logger::danger("missing composer.phar");
		// 	return -1;
		// }
		$arg = implode(" ", array_filter($args));
		chdir($packages);
		Logger::info("{$composer} {$arg}");
		$o = `$composer $arg `;
		echo $o;
	}
}