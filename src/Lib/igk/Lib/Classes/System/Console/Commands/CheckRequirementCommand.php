<?php
// @author: C.A.D. BONDJE DOUE
// @file: CheckRequirementCommand.php
// @date: 20231019 14:09:26
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Requirement;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands
*/
class CheckRequirementCommand extends AppExecCommand{
	var $command='--check-requirement';
	var $desc='check requirement'; 
	/* var $options=[]; */
	var $category='system'; 
	public function exec($command) { 
		$rq = new Requirement();
		if (!$rq->check()){
			$v_requirements = $rq->getRequirements();
			foreach($v_requirements as $m){
				Logger::print($m['msg']);
			}
			return -1;
		}
		Logger::success('all php requirements passed');
	
	}
}