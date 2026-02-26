<?php
// @author: C.A.D. BONDJE DOUE
// @file: ChangeUserCommand.php
// @date: 20230726 19:09:53
namespace IGK\System\Console\Commands;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
/**
 * 
 * @package IGK\System\Console\Commands
 */
class ChangeUserCommand extends AppExecCommand
{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command = '--users:change';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc = 'change user\'s definition';
	/* var $options=[]; */

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category = self::USER_CAT;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $usage = 'login [--set:column=value] [options]';

    /**
    * auto generate doc.
    * @param mixed $command
    * @param null|string $login
    */
    public function exec($command, ?string $login = null)
	{
		is_null($login) && igk_die('required login');
		$user = igk_get_user_bylogin($login) ?? igk_die('user not found');
		$set = igk_getv($command->options, '--set');
		if ($set) {
			if (is_array($set)) {
				foreach ($set as $l) {
					$b  = explode('=', $l);
					$k = array_shift($b);
					$v = implode('=', $b);
					$user->$k = $v;
				}
			} else {
				$b  = explode('=', $set,2);
				$k = $b[0];
				$user->$k = igk_getv($b, 1);
			}
		} else {
			Logger::warn("missing information --set");
		}
		if ($user->save()) {
			Logger::success('done');
		} else {
			Logger::danger('error!');
		}
	}
}