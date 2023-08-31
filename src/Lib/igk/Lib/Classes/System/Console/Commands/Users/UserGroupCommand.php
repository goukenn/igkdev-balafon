<?php
// @author: C.A.D. BONDJE DOUE
// @file: UserGroupCommand.php
// @date: 20230704 14:22:51
namespace IGK\System\Console\Commands\Users;

use IGK\Controllers\SysDbController;
use IGK\Database\Macros\UsersMacros;
use IGK\Helper\Authorization;
use IGK\Helper\ModelHelper;
use IGK\Helper\SysUtils;
use IGK\System\Console\AppExecCommand;
use JSon;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands\Users
*/
class UserGroupCommand extends AppExecCommand{
	var $command='--users:bind-group';
	var $desc='bind user to group. prefix with (-) sign to remove from group.';
	var $options=[
		'--controller'=>'set controller'
	];
	var $category='user';
	var $usage = 'user group[] [options]';

	public function exec($command, string $user=null, ...$groups) {  
		$user = igk_get_user_bylogin($user) ?? igk_die('missing user');	
		$ctrl = SysDbController::ctrl();
		if (
			$m = igk_getv($command->options, '--controller')){
				$ctrl = self::GetController($m);
		};
		foreach($groups as $g){
			if ($g[0]=='-'){
				Authorization::UnbindUserFromGroup($ctrl, $user, substr($g,1));			
			}else{
				Authorization::BindUserToGroup($ctrl, $user, $g);			
			}
		}
		$g = array_map(ModelHelper::MapToArray(),  $user->groups());
		$data = ['groups'=>$g];
		echo json_encode((object)$data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) .PHP_EOL; 
		return 0;
	}
}