<?php
// @author: C.A.D. BONDJE DOUE
// @file: dropUserCommand.php
// @date: 20250427 08:59:28
namespace IGK\System\Console\Commands;
use IGK\Models\Usergroups as ModelsUsergroups;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger; 
/**
 * drop user 
 * @package IGK\System\Console\Commands
 * @author C.A.D. BONDJE DOUE
 */
class dropUserCommand extends AppExecCommand
{
	var $command = '--users:remove';
	var $desc='remove user'; 
	/* var $options=[]; */
	var $category = 'users'; 
	var $usage = 'login [option]';
	public function exec($command, $userid = null)
	{
		$user = igk_get_user_bylogin($userid);
		if (!$user) {
			igk_die('missing user');
		}
		$v_hook = 'sys://database/drop/system_user';
		igk_reg_hook($v_hook, function($e){
			$user = $e->args['user'];
			$s = true;
			$s = $s && ModelsUsergroups::delete([
				ModelsUsergroups::FD_CL_USER_ID=>$user->clId
			]);
		});
		$ad = $user->getDataAdapter();
		try {
			$ad->beginTransaction();
			igk_hook($v_hook, ['user' => $user]);
			if ($user->delete()) {
				$ad->commit();
				Logger::success('user delete. '.$user->clLogin);
			}
		} catch (\Exception $ex) {
			$ad->rollback();
			Logger::danger($ex->getMessage());
		}
	}
}