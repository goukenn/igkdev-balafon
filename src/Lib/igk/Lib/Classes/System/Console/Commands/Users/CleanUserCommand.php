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

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command='--users:clean-user';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc='remove user';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $options=[];

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category = 'users';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $usage = 'login [options]';

    /**
    * auto generate doc.
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