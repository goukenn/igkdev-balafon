<?php
// @author: C.A.D. BONDJE DOUE
// @file: CommandRegisterCommand.php
// @date: 20250908 10:26:59
namespace IGK\System\Console\Commands\Commands;

use IGK\System\Console\App;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\EnvironmentCommandScripts;
use IGK\System\Console\Logger;

/**
 * 
 * @package IGK\System\Console\Commands\Commands
 * @author C.A.D. BONDJE DOUE
 */
class CommandRegisterCommand extends AppExecCommand
{
	var $command = '--command:ls';
	var $desc = 'list registered command';
	var $options = [];
	var $category = '';
	var $usage = '';
	public function exec($command)
	{
		$def = EnvironmentCommandScripts::GetCacheDefinition();
		Logger::print('list registered commands');
		foreach ($def as $k => $v) {
			$dt = [App::Gets(App::GREEN, $k)];
			$dt[] = $v->desc;
			Logger::info(implode("\r\t\t\t", $dt));
		}

		return -1;
	}
}
