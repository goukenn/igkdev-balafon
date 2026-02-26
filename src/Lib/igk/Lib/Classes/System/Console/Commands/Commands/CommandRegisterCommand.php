<?php
// @author: C.A.D. BONDJE DOUE
// @file: CommandRegisterCommand.php
// @date: 20250908 10:26:59
namespace IGK\System\Console\Commands\Commands;

use IGK\System\Console\App;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\EnvironmentCommandScripts;
use IGK\System\Console\Logger;
use function igk_resources_gets as __;

/**
 * 
 * @package IGK\System\Console\Commands\Commands
 * @author C.A.D. BONDJE DOUE
 */
class CommandRegisterCommand extends AppExecCommand
{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command = '--command:ls';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc = 'list registered command';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $options = [
		'--location' =>'flag: show default location'
	];

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category = 'command';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $usage = '[options]';

    /**
    * auto generate doc.
    * @param mixed $command
    */
    public function exec($command)
	{
		$def = EnvironmentCommandScripts::GetCacheDefinition();
		if (property_exists($command->options, '--location')){
			Logger::print(__('commands locations'));
			Logger::info(json_encode(['commandLocation'=>EnvironmentCommandScripts::DefaultCommandLocation()],
			 JSON_PRETTY_PRINT |
			JSON_UNESCAPED_SLASHES));
			Logger::print('');
		}
		Logger::print(__('list registered commands'));
		foreach ($def as $k => $v) {
			$dt = [App::Gets(App::GREEN, $k)];
			$dt[] = $v->desc;
			Logger::info(implode("\r\t\t\t", $dt));
		}

		return -1;
	}
}
