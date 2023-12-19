<?php
// @author: C.A.D. BONDJE DOUE
// @file: InitDbCommand.php
// @date: 20230703 12:55:25
namespace IGK\System\Console\Commands\Database;

use IGK\Controllers\SysDbController;
use IGK\Helper\SysUtils;
use IGK\Models\Users;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\BalafonApplication;
use IGK\System\Console\Commands\DbCommandHelper;
use IGK\System\Console\Logger;
use IGKModuleListMigration;
use L81Controller;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands\Database
*/
class InitDbCommand extends AppExecCommand{
	var $command="--db:initdb";
    var $desc='init databases';
	var $options=[
		'--clean'=>'flag: enable drop database if exists',
		'--force'=>'flag: force file creation'
	];
	var $category = "db";
	var $usage = '[controller] [options]';

	public function exec($command, ?string $ctrl = null) { 
		$c = null;
		DbCommandHelper::Init($command);
		$clean = false;
		if (empty($ctrl)){
			$ctrl = igk_getv($command->options,"--controller");
		}
		if (!empty($ctrl)) {
			if (!($c = igk_getctrl($ctrl, false))) {
				Logger::danger("no controller found: " . $ctrl);
				return -1;
			}
			$c = [$c];
		} else {		
			// $ad = Users::model()->getDataAdapter();	
			// $ad->sendQuery('drop database `igkdev.ops2`;');
			// $ad->sendQuery('create database `igkdev.ops2`;');
			// $ad->selectdb('igkdev.ops2');

			$c = igk_sys_getall_ctrl();   
			// $c = [ L81Controller::ctrl()];           
			if ($b = IGKModuleListMigration::CreateModulesMigration()) {
				$c = array_merge($c, [$b]);
			}
			SysUtils::PrependSysDb($c);
			$clean = property_exists($command->options, '--clean');
		}
		$force = property_exists($command->options, '--force');

		if ($c) {
			$db_name = igk_configs()->db_name;
			Logger::info('dbname :'. $db_name);
			if ($clean){
				// igk_db_environment()->no_db_select = true;
				igk_set_env("sys://Db/NODBSELECT", true );
				if ($ad = SysDbController::getDataAdapter()){
					$ad->setNoSelectDbErrorAutoClose(true);
					if ($ad->connect()){
						$ad->sendQuery(sprintf('DROP Database IF EXISTS `%s`', $db_name));
						$ad->sendQuery(sprintf('CREATE DATABASE IF NOT EXISTS `%s` charset=\'utf8\';', $db_name));
						$ad->selectdb($db_name);
						$ad->close();
					}
				}
			}
			foreach ($c as $m) {
				BalafonApplication::BindCommandController($m, null);
				$cl = get_class($m);
				if ($m->getCanInitDb()) {
					Logger::info("init-db: " . $cl);			
					$m::initDb($force);
					Logger::success("complete: " . $cl);
				} else {
					Logger::warn("can't initdb of " . $cl);
				}
			}
			return 1;
		}
		return -1;
	}
}