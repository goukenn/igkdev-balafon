<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbInitDataCommand.php
// @date: 20230802 20:49:12
namespace IGK\System\Console\Commands;
use IGK\Helper\Database;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Database\InitBase;
use IGK\System\EntryClassResolution;

/**
* auto generate doc.
* @package IGK\System\Console\Commands
*/
class DbInitDataCommand extends AppExecCommand{
    /**
    * Property: command.
    * @var mixed
    */
    var $command='--db:initdata';
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc='initialize data command'; 
	/* var $options=[]; */
    /**
    * Property: category.
    * @var mixed
    */
    var $category='db';
    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = 'controller [action_name] [options]';
    /**
    * Exec.
    * @param mixed $command
    * @param null|string $controller
    * @param null|string $action_name
    */
    public function exec($command, ?string $controller = null, ?string $action_name=null) {
		is_null($controller) && igk_die('required controller');
		($ctrl = self::GetController($controller)) ?? igk_die('missing controller');
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
		} else  {
			Logger::info('initailize db. with [./InitBase]');
			Database::InitData($ctrl);
			Logger::success('done');
		}
	 }
}