<?php
// @author: C.A.D. BONDJE DOUE
// @file: ServicesCommand.php
// @date: 20260212 20:27:15
namespace IGK\System\Console\Commands\Services;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGKServices;

/**
* 
* @package IGK\System\Console\Commands\Services
* @author C.A.D. BONDJE DOUE
*/
class ServicesCommand extends AppExecCommand{
	var $command='--services';
	var $desc='service management command.'; 
	var $options=[];
	var $category = 'sys';
	var $usage = 'action* [options]'; 
	public function exec($command) { 

		$srv = IGKServices::getInstance();
		$l = $srv->services();
		$def = [];
		if ($l){
			$def['registrated']=array_keys($l);
		}
		igk_wln(json_encode($def, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
		Logger::success('done. service');
	}
	/**
	 * show service informations 
	 * @return void 
	 */
	protected function _service_info(){

	}
}