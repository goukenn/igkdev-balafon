<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegisterLoginToCommand.php
// @date: 20230713 14:43:57
namespace IGK\System\Console\Commands\Users;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;

/**
* auto generate doc.
* @package IGK\System\Console\Commands\Users
*/
class RegisterLoginToCommand extends AppExecCommand{
    /**
    * Property: command.
    * @var mixed
    */
    var $command='--users:login';
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc='login command. to register to project';
	/* var $options=[]; */
    /**
    * Property: category.
    * @var mixed
    */
    var $category = self::USER_CAT;
    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = 'controller login [options]';

    var $options = [
        '--controller:'=>'the controller to use'
    ];
    /**
    * Exec.
    * @param mixed $command
    * @param null|string $controller
    * @param null|string $login
    */
    public function exec($command, ?string $controller=null, ?string $login = null) { 
        if (empty($login)){
            if ($c = igk_getv($command->options, '--controller')){
                $login = $controller;
                $controller = $c;
            }
        }
		$login || igk_die("require login");
		$ctrl = self::GetController($controller);
		$user = igk_get_user_bylogin($login) ?? igk_die('missing user');
		$ctrl->register_autoload();
		 $result = $ctrl->login($user);
		 if (!$result){
			Logger::danger("failed");
			return -1;
		 }
		 Logger::success('OK');
	}
}