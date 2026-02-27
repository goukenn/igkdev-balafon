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
* auto generate doc.
* @package IGK\System\Console\Commands\Users
* @author C.A.D. BONDJE DOUE
*/
class CleanUserCommand extends AppExecCommand{

    /**
    * Property: command.
    * @var mixed
    */
    var $command='--users:clean-user';

    /**
    * Property: desc.
    * @var mixed
    */
    var $desc='remove user';

    /**
    * Property: options.
    * @var mixed
    */
    var $options=[];

    /**
    * Property: category.
    * @var mixed
    */
    var $category = 'users';

    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = 'login [options]';

    /**
    * Exec.
    * @param mixed $command
    * @param null|string $login
    */
    public function exec($command, ?string $login = null) { 
		!$login && igk_die('missing login');  
		$user = igk_get_user_bylogin($login) ?? igk_die('missing user'); 
		$user->cleanAndDrop();
		Logger::success('done');

	}
}