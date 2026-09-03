<?php
// @author: C.A.D. BONDJE DOUE
// @file: MakeScriptCommand.php
// @date: 20260830 13:47:19
namespace IGK\System\Console\Commands;

use IGK\Helper\IO;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Helper\ConsoleUtility;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\System\IO\Path;

/**
 * 
 * @package IGK\System\Console\Commands
 * @author C.A.D. BONDJE DOUE
 */
class MakeScriptCommand extends AppExecCommand
{
	var $command = '--make:script-command';
	var $desc = 'use to create a script command';
	var $options = [];
	var $category = 'make';
	var $usage = 'file [options]';
	public function exec($command, ?string $file = null)
	{

		empty($file) && igk_die('file required');
		$pwd = igk_server()->PWD;
		$f = IO::IsAbsolutePath($file) ? $file : Path::Combine($pwd, $file);
		$bind[$f] = function ($f) {
			$b = new PHPScriptBuilder;
			$b->type('function')
				->defs(
					implode("\n", [
						"/**",
						'* @var mixed $params',
						'* @var mixed $command',
						"*/"
					])
				);

			igk_io_w2file($f, $b->render());
		};
		ConsoleUtility::MakeBindFiles($command, $bind, false);
	}
}
