<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegisterLoginToCommand.php
// @date: 20230713 14:43:57
namespace IGK\System\Console\Commands\Users;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands\Users
*/
class RegisterLoginToCommand extends AppExecCommand{
	var $command='--users:login';

	var $desc='login command. to register to project';
	/* var $options=[]; */
	var $category = 'user';

	var $usage = 'controller login [options]';

	public function exec($command, ?string $controller=null, ?string $login = null) { 
		$login || igk_die("require login");
		$ctrl = self::GetController($controller);
		$user = igk_get_user_bylogin($login) ?? igk_die('missing user');
		$ctrl->register_autoload();
		 $result = $ctrl->login($user);
		 if (!$result){
			Logger::danger("failed");
			exit(-1);
		 }
		 Logger::success('OK');
		igk_exit();
	}
}