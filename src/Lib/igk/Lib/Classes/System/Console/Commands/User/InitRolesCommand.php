<?php
// @author: C.A.D. BONDJE DOUE
// @file: InitRolesCommand.php
// @date: 20251112 13:47:34
namespace IGK\System\Console\Commands\User;

use IGK\Database\Helper\DbInitManagement;
use IGK\Models\Users;
use IGK\System\Console\AppExecCommand;
use IGKEvents;

/**
* 
* @package IGK\System\Console\Commands\User
* @author C.A.D. BONDJE DOUE
*/
class InitRolesCommand extends AppExecCommand{
	var $command='--users:init-role';
	var $desc='initiliaze user\'s role';
	var $options=[
		'--controller'=>'host controller',
		'--reset'=>'reset all authorization',
	];
	var $category = 'users';
	var $usage = '';
	public function exec($command, ?string $user = null) { 
		
		!$user && igk_die('require user');
		$ctrl = igk_getv($command->options, '--controller');
		$reset = property_exists($command->options, '--reset');
		$ctrl = self::GetController($ctrl);

		DbInitManagement::InitControllerProfile($ctrl, $reset);

		$user = Users::Get("clLogin", $user); 
		igk_hook(IGKEvents::HOOK_USER_ADDED, compact('user', 'ctrl'));
		$ctrl::bindUserDefaultProfiles($user);
	}
}