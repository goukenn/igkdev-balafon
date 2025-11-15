<?php
// @author: C.A.D. BONDJE DOUE
// @file: CleanUserCommand.php
// @date: 20251113 07:27:31
namespace IGK\System\Console\Commands\Users;

use com\igkdev\projects\ForemJobDashboard\Models\Jobs;
use IGK\Models\Usergroups;
use IGK\Models\Users;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGKEvents;

/**
* 
* @package IGK\System\Console\Commands\Users
* @author C.A.D. BONDJE DOUE
*/
class CleanUserCommand extends AppExecCommand{
	var $command='--users:clean-user';
	var $desc='remove user';
	var $options=[];
	var $category = 'users';
	var $usage = 'login [options]';
	public function exec($command, ?string $login = null) { 
		!$login && igk_die('missing login');  
		$user = igk_get_user_bylogin($login) ?? igk_die('missing user'); 
		$user->cleanAndDrop();
		Logger::success('done');

	}
}