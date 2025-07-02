<?php
// @author: C.A.D. BONDJE DOUE
// @file: ListComponentPackageCommand.php
// @date: 20250508 02:52:58
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;

/**
* 
* @package IGK\System\Console\Commands
* @author C.A.D. BONDJE DOUE
*/
class ListComponentPackageCommand extends AppExecCommand{
	var $command='--component-package:list';
	var $desc='list system\'s node package namespaces'; 
	/* var $options=[]; */
	var $category = 'sys';
	/* var $usage = ''; */
	public function exec($command) { 

		$l = igk_reg_component_package();
		ksort($l);
		Logger::print(implode("\n", array_keys($l)));
	}
}