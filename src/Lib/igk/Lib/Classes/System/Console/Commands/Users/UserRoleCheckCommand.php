<?php
// @author: C.A.D. BONDJE DOUE
// @file: UserRoleCheckCommand.php
// @date: 20230704 15:08:24
namespace IGK\System\Console\Commands\Users;

use IGK\System\Console\App;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands\Users
*/
class UserRoleCheckCommand extends AppExecCommand{
	var $command='--users:role-check';
	var $desc='check user\'s roles';
	/* var $options=[]; */

	var $category = self::USER_CAT;
	var $usage = 'login auth [ctrl] [options]';
	// CarRentalController@ProposeCar
	public function exec($command, string $user=null, string $auth=null, ?string $ctrl = null) {  
		$user = igk_get_user_bylogin($user) ?? igk_die('missing user');		
		if ($ctrl){
			$ctrl = self::GetController($ctrl);
			$auth = $ctrl->authName($auth);
		}
		Logger::print('check : '. $auth. ' ? '.($user->auth($auth) ?  App::Gets( App::GREEN, 'granted') : App::Gets(App::RED, 'denied') ));
		return 0;
	}
}