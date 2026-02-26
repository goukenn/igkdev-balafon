<?php
// @author: C.A.D. BONDJE DOUE
// @file: UserRoleCommand.php
// @date: 20230704 14:17:10
namespace IGK\System\Console\Commands\Users;
use IGK\Helper\ModelHelper;
use IGK\System\Console\AppExecCommand;
/**
* 
* @package IGK\System\Console\Commands\Users
*/
class UserRoleCommand extends AppExecCommand{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command='--users:role';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc='get user\'s roles';
	/* var $options=[]; */

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category = self::USER_CAT;

    /**
    * auto generate doc.
    * @param mixed $command
    * @param null|string $user
    */
    public function exec($command, ?string $user=null) {  
		$user = igk_get_user_bylogin($user) ?? igk_die('missing user');		
		$auths = $user->auths();
		//$roles = $user::role();
		$data = [
			'member_of'=> array_map(ModelHelper::MapToArray(),  $user->groups()), 
			'authorizations'=> array_map(ModelHelper::MapToArray(), $auths)
		];		
		echo json_encode((object)$data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) .PHP_EOL; 
		return 0;
	}
}