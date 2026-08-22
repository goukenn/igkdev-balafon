<?php
// @author: C.A.D. BONDJE DOUE
// @file: SystemComponentCommand.php
// @date: 20260818 16:32:46
namespace IGK\System\Console\Commands\Core\Html\Dom;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;

/**
 * 
 * @package IGK\System\Console\Commands\Core\Html\Dom
 * @author C.A.D. BONDJE DOUE
 */
class SystemComponentCommand extends AppExecCommand
{
	var $command = '--components';
	var $desc = 'retrieve registrated system component';
	var $options = [];
	var $category = 'dom';
	var $usage = '';
	public function exec($command)
	{
		$func = igk_getv(get_defined_functions(), 'user');
		sort($func);
		$c = [];
		$sep = str_repeat('-', 50);
		foreach ($func as $a) {
			if (preg_match('/^igk_html_node_(.+)$/', $a, $tab)) {
				$c[strtolower($tab[1])] = 1;
			}
		}
		Logger::info('# Registrated server component: ');
		Logger::print($sep);
		igk_wln(json_encode(array_keys($c)));

		if ($packages = igk_component_packages() ?? []) {
			echo PHP_EOL;
			Logger::info('# Packages used to creat specialized component');
			Logger::print($sep);
			foreach ($packages as $k => $v) {
				Logger::warn('- '.$k);
			}
		}
	}
}
