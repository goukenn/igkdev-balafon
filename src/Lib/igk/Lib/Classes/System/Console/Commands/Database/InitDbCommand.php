<?php
// @author: C.A.D. BONDJE DOUE
// @file: InitDbCommand.php
// @date: 20230703 12:55:25
namespace IGK\System\Console\Commands\Database;
use Exception;
use IGK\Controllers\SysDbController;
use IGK\Helper\SysUtils;
use IGK\Models\Users;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\BalafonApplication;
use IGK\System\Console\Commands\DbCommandHelper;
use IGK\System\Console\Logger;
use IGKException;
use IGKModuleListMigration;
use L81Controller;

/**
* 
* @package IGK\System\Console\Commands\Database
*/
/**
* auto generate doc.
* @package IGK\System\Console\Commands\Database
*/
class InitDbCommand extends AppExecCommand{
    /**
    * Property: command.
    * @var mixed
    */
    var $command="--db:initdb";
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc='init databases';
    /**
    * Property: options.
    * @var mixed
    */
    var $options=[
		'--clean'=>'flag: enable drop database if exists',
		'--force'=>'flag: force file creation',
	];
    /**
    * Property: category.
    * @var mixed
    */
    var $category = "db";
    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = '[controller] [options]';
    /**
    * auto generate doc.
    * @param null|string $ctrl
    * @return int
    */
    public function exec($command, ?string $ctrl = null) { 
		$c = null;
		DbCommandHelper::Init($command);
		$clean = false;
		if (empty($ctrl)){
			$ctrl = igk_getv($command->options,"--controller");
		}
		if (!empty($ctrl)) {
			if (!($c = self::GetController($ctrl, false))) {
				Logger::danger("no controller found: " . $ctrl);
				return -1;
			}
			$c = [$c];
		} else {		
			$c = igk_sys_getall_ctrl();   
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