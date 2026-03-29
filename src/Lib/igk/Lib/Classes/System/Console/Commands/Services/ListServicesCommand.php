<?php
// @author: C.A.D. BONDJE DOUE
// @file: ListServicesCommand.php
// @date: 20250815 16:38:34
namespace IGK\System\Console\Commands\Services;
use IGK\System\Console\AppExecCommand;
use IGKServices;
/**
* auto generate doc.
* @package IGK\System\Console\Commands\Services
* @author C.A.D. BONDJE DOUE
*/
class ListServicesCommand extends AppExecCommand{
    /**
    * Property: command.
    * @var mixed
    */
    var $command='--services:list';
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc='list registrated services provider';
	/* var $options=[]; */
	/* var $category = ''; */
    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = '[options]';
    /**
    * Exec.
    * @param mixed $command
    */
    public function exec($command) { 
		$ic = IGKServices::getInstance();
		if ($ic instanceof IGKServices){
			$l = $ic->services();
			$v_keys = array_keys($l);
			sort($v_keys);
			igk_wln_e(json_encode(array_keys($l), JSON_PRETTY_PRINT));
		}
	}
}