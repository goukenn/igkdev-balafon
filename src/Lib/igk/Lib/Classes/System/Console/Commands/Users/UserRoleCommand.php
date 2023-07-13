<?php
// @author: C.A.D. BONDJE DOUE
// @file: UserRoleCommand.php
// @date: 20230704 14:17:10
namespace IGK\System\Console\Commands\Users;

use IGK\Helper\ModelHelper;
use IGK\System\Console\AppExecCommand;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands\Users
*/
class UserRoleCommand extends AppExecCommand{
	var $command='--users:role';
	var $desc='get user\'s roles';
	/* var $options=[]; */
	var $category='user';

	public function exec($command, string $user=null) {  
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