<?php
// @author: C.A.D. BONDJE DOUE
// @file: ActivateCommand.php
// @date: 20240927 15:47:17
namespace IGK\System\Console\Commands\Users;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
/**
* 
* @package IGK\System\Console\Commands\Users
* @author C.A.D. BONDJE DOUE
*/
class ActivateCommand extends AppExecCommand{

    /**
    * Property: command.
    * @var mixed
    */
    var $command='--users:activate';

    /**
    * Property: desc.
    * @var mixed
    */
    var $desc='activate the current user';
	/* var $options=[]; */

    /**
    * Property: category.
    * @var mixed
    */
    var $category = 'users';

    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = 'login';

    /**
    * Exec.
    * @param mixed $command
    * @param null|string $login
    */
    public function exec($command, ?string $login = null) { 
		$login || igk_die("missing users");
		$user = igk_get_user_bylogin($login) ?? igk_die("user not found");
		$user->clStatus = 1;
		$user->save();
		Logger::success("done");
	 }
}