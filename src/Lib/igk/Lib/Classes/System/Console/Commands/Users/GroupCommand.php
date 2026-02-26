<?php
// @author: C.A.D. BONDJE DOUE
// @file: GroupCommand.php
// @date: 20230802 13:27:05
namespace IGK\System\Console\Commands\Users;
use IGK\Helper\JSon;
use IGK\Models\Users;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
/**
* 
* @package IGK\System\Console\Commands\Users
*/
class GroupCommand extends AppExecCommand{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command='--users:group';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc='view user\'s group';
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
    var $usage = 'login [option]';

    /**
    * auto generate doc.
    * @param mixed $command
    * @param null|string $login
    */
    public function exec($command, ?string $login=null) {
		is_null($login) && igk_die('login required');
		$user = igk_get_user_bylogin($login);
		$groups = $user->groups();
		Logger::info( "group result : "); 
		echo JSon::Encode($groups, null, JSON_PRETTY_PRINT);
		exit;
	}
}