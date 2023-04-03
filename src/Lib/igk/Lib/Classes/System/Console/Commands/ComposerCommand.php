<?php
// @author: C.A.D. BONDJE DOUE
// @file: ComposerCommand.php
// @date: 20230311 09:12:52
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Shell\OsShell;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commmands
*/
class ComposerCommand extends AppExecCommand{
	var $command='--composer';
	var $desc='help manage balafon composer packages';
	/* var $options=[]; */
	var $category = 'composer';
	public function exec($command, string ...$args) { 
		$packages = igk_io_packagesdir();

		if (empty($composer = OsShell::where("composer.phar"))){
			$composer = $packages."/composer.phar";
		}
		if (!$composer || !file_exists($composer))
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