<?php
// @author: C.A.D. BONDJE DOUE
// @file: BalafonInitCommand.php
// @date: 20231019 13:07:41
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\BalafonInitEnvironment;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands
*/
class BalafonInitCommand extends AppExecCommand{
	var $command='--init';
	var $desc='initiliaze environment'; 
	var $options=[
		'--noconfig'=>'flag: enabled ',
		'--force'=>'flag: fore re-creation', 
		'--primary'=>'flag: if --noconfig initialize activate the primary file generation'
	]; 
	var $category='system';
	var $usage = '[options]'; 
	public function exec($command) {
		return (new BalafonInitEnvironment())->run($command); 
	 }
}