<?php
// @author: C.A.D. BONDJE DOUE
// @file: RunHookCommand.php
// @date: 20260323 19:49:30
namespace IGK\System\Console\Commands\Core;
use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use IGK\Helper\StringUtility;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Helper\ConsoleUtility;
use IGK\System\Console\Logger;
use IGK\System\EntryClassResolution;
use IGK\System\Html\Css\CssParser;
use IGK\System\IO\Configuration\ConfigurationReader;
use IGK\Tests\BaseTestCase\ConnexionStringTest;
use IGKEvents;

/**
* auto generate doc.
* @package IGK\System\Console\Commands\Core
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\Console\Commands\Core
*/
class RunHookCommand extends AppExecCommand
{
	var $command = '--run:hook';
    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    var $desc = 'run hooks';
    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    var $options = [
		'--controller' => 'base controller',
		'--info' => 'ask for hook command line informations'
	];
	/* var $category = ''; */
    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    var $usage = 'hookName ...args [options]';
    /**
    * auto generate doc.
    * @param mixed $command
    * @param null|string $hookName
    * @return
    */
    public function exec($command, ?string $hookName = null)
	{
		is_null($hookName) && igk_die('require hookName');
		$args = array_slice(func_get_args(), 2);
		$arguments = [];
		$ctrl = igk_getv($command->options, '--controller');
		$ctrl = $ctrl ? self::GetController($ctrl, 1, true) : null;
		$info = property_exists($command->options, '--info');
		if ($ctrl) {
			$hookName = $ctrl::hookName($hookName);
		} else {
			$ctrl = SysDbController::ctrl(true);
		}
		if ($info) {
			return $this->showHookNameInfo($hookName, $ctrl);
		}
		$this->buildArgument($ctrl, $hookName, $arguments, $args);
		igk_hook($hookName, $arguments);
	}
    /**
    * auto generate doc.
    * @param mixed $ctrl
    * @param mixed $hookName
    * @param array & $arguments
    * @param array $args
    * @return
    */
    public function buildArgument($ctrl, $hookName, array &$arguments, array $args)
	{
		if (count($args) == 1) {
			$reader = new ConfigurationReader;
			$reader->separator = ':';
			$reader->delimiter = ';';
			$config = $reader->read($args[0]);
			$args = (array)$config;
		}
		if ($cl = self::GetControllerCommandClassInfo($ctrl, $hookName)) {
			$o = new $cl;
			if (method_exists($o, 'filter')) {
				$arguments = $o->filter($args);
			}
			if (method_exists($o, 'hook')) {
				igk_reg_hook($hookName, [$o, 'hook']);
			}
		}
		$arguments['ctrl'] = $ctrl;
	}
    /**
    * auto generate doc.
    * @param BaseController $ctrl
    * @param string $hookName
    * @return
    */
    public static function GetControllerCommandClassInfo(BaseController $ctrl, string $hookName)
	{
		$n = $ctrl->getName();
		$n = preg_replace('/^' . $n . '\\b/i', '', $hookName);
		$tab = preg_split('/[^a-z0-9]/i', $n);
		$n = ltrim(implode('/', array_map('ucfirst', array_filter($tab))), '/');
		$cl = $n;
		if ($cl = $ctrl::resolveClass($tcl = EntryClassResolution::COMMAND_HELP_INFO_NS  . $cl)) {
			return $cl;
		}
		return null;
	}
    /**
    * auto generate doc.
    * @param string $hookName
    * @param BaseController $ctrl
    * @return
    */
    public function showHookNameInfo(string $hookName, BaseController $ctrl)
	{
		$options = [];
		igk_hook(IGKEvents::FILTER_RUN_HOOK_COMMAND_INFO, ['name' => $hookName, 'ctrl' => $ctrl, 'options' => &$options]);
		if (!$options) {
			if ($cl = self::GetControllerCommandClassInfo($ctrl, $hookName)) {
				$o = new $cl;
				$options = array_merge($options, $o->info() ?? []);
			}
		}
		if ($options) {
			Logger::print(sprintf('Hook Info : [%s]', $hookName));
			ConsoleUtility::ShowOptionsCommand($options);
		} else {
			igk_wln('no hook definition');
		}
	}
}