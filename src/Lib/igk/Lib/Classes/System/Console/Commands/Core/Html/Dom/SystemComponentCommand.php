<?php
// @author: C.A.D. BONDJE DOUE
// @file: SystemComponentCommand.php
// @date: 20260818 16:32:46
namespace IGK\System\Console\Commands\Core\Html\Dom;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
/**
* auto generate doc.
* @package IGK\System\Console\Commands\Core\Html\Dom
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\Console\Commands\Core\Html\Dom
*/
class SystemComponentCommand extends AppExecCommand
{
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    var $command = '--components';
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    var $desc = 'retrieve registrated system component';
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    var $options = [];
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    var $category = 'dom';
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    var $usage = '';
    /**
    * auto generate doc.
    * @param mixed $command
    * @return void
    */
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
