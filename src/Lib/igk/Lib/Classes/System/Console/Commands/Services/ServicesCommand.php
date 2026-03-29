<?php
// @author: C.A.D. BONDJE DOUE
// @file: ServicesCommand.php
// @date: 20260212 20:27:15
namespace IGK\System\Console\Commands\Services;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGKServices;
/**
* auto generate doc.
* @package IGK\System\Console\Commands\Services
* @author C.A.D. BONDJE DOUE
*/
class ServicesCommand extends AppExecCommand{
    /**
    * Property: command.
    * @var mixed
    */
    var $command='--services';
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc='service management command.';
    /**
    * Property: options.
    * @var mixed
    */
    var $options=[];
    /**
    * Property: category.
    * @var mixed
    */
    var $category = 'sys';
    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = 'action* [options]';
    /**
    * Exec.
    * @param mixed $command
    */
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