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
* auto generate doc.
* @package IGK\System\Console\Commands\User
* @author C.A.D. BONDJE DOUE
*/
class InitRolesCommand extends AppExecCommand{
    /**
    * Property: command.
    * @var mixed
    */
    var $command='--users:init-role';
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc='initiliaze user\'s role';
    /**
    * Property: options.
    * @var mixed
    */
    var $options=[
		'--controller'=>'host controller',
		'--reset'=>'reset all authorization',
	];
    /**
    * Property: category.
    * @var mixed
    */
    var $category = 'users';
    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = '';
    /**
    * Exec.
    * @param mixed $command
    * @param null|string $user
    */
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