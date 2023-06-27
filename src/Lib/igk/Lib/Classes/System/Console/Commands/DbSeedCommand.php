<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbSeedCommand.php
// @date: 20230509 08:40:53
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands
*/
class DbSeedCommand extends AppExecCommand{
	var $command='--db:seed';
	var $desc='seed controller\'s database';
	var $category='db'; 
	var $usage  = "[controller] [class] [options]";
	/* var $options=[]; */
	public function exec($command, $ctrl = null, $class = null) {
		DbCommandHelper::Init($command);
		// Transformo to namespace class
		DbCommandHelper::Seed($ctrl, $class); 
		return 0; 
	}
}