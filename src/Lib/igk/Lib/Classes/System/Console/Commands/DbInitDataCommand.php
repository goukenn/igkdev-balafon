<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbInitDataCommand.php
// @date: 20230802 20:49:12
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Database\InitBase;
use IGK\System\EntryClassResolution;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands
*/
class DbInitDataCommand extends AppExecCommand{
	var $command='--db:initdata';
	var $desc='initialize data command'; 
	/* var $options=[]; */
	var $category='db';
	var $usage = 'controller [action_name] [options]';
	public function exec($command, ?string $controller = null, ?string $action_name=null) {
		is_null($controller) && igk_die('required controller');
		$ctrl = self::GetController($controller);
		 
		$cl = $ctrl->resolveClass(EntryClassResolution::DbInitData) ?? igk_die('init data class is missing');

		if ($action_name)
		{
			if (method_exists($cl, $action_name)){

				call_user_func_array([$cl, $action_name], [$ctrl]);
				Logger::success('done');
			}
			else {
				igk_die(sprintf('missing action name in %s', $cl));
			}

		} else 
		{
			call_user_func_array([$cl, InitBase::INIT_METHOD], [$ctrl]);
			Logger::success('done');

		}
		igk_exit(-1);

	 }
}