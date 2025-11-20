<?php
// @author: C.A.D. BONDJE DOUE
// @file: ChangePasswordCommand.php
// @date: 20250208 17:03:57
namespace IGK\System\Console\Commands\Users;
use IGK\Models\Users;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
/**
* 
* @package IGK\System\Console\Commands\Users
* @author C.A.D. BONDJE DOUE
*/
class ChangePasswordCommand extends AppExecCommand{
	var $command='--user:change-pwd';
	var $desc='change user\'s password'; 
	var $options=[];
	var $category = 'users'; 
	var $usage = 'user new_password [options]'; 
	public function exec($command, ?string $user=null, ?string $newPassword = null) { 
		if ($newPassword && $user && ($v_tu = igk_get_user_bylogin($user))){ 
			$v_tu->changePassword($newPassword);
			Logger::success("done");
		}else {
			Logger::danger("failed to change password");
			return -1;
		}  
	}
}