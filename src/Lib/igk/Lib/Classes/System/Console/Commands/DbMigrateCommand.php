<?php

// @author: C.A.D. BONDJE DOUE
// @filename: DbMigrateCommand.php
// @date: 20221111 22:30:40
// @desc: 

namespace System\Console\Commands;

use IGK\Controllers\ControllerExtension;
use IGK\Controllers\SysDbController;
use IGK\Helper\SysUtils;
use IGK\System\Caches\DBCaches;
use IGK\System\Caches\DBCachesModelInitializer;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Commands\DbCommandHelper;
use IGK\System\Console\Logger;
use IGK\System\Database\MigrationHandler;
use IGKModuleListMigration;

class DbMigrateCommand extends AppExecCommand
{

    var $command = '--db:migrate';

    var $category = 'db';

    var $desc = 'migration command';

    var $options = [
        '--no-clear-db-cache'=>'flag: do not clear db cache'
    ];


    public function showUsage(){
        parent::showCommandUsage('controller [options]');
    }

    public function exec($command, $ctrl = null)
    {
        DbCommandHelper::Init($command);
        if (!is_null($ctrl)) {
            if (($c = self::GetController($ctrl, false))) {
                $c = [$c];
            } else {
                igk_die("missing controller : " . $ctrl);
            }
        } else {
            $c = igk_sys_getall_ctrl();
            if (($ctrl === null) && ($modules = igk_get_modules())) {
                $list = array_filter(array_map(function ($c, $k) {
                    if ($mod = igk_get_module($k)) {
                        return $mod;
                    }
                }, $modules, array_keys($modules)));
                SysUtils::PrependSysDb($c);
                $c = array_merge($c, [IGKModuleListMigration::Create($list)]);
            }
        }
        if (!$c){
            Logger::danger('no controller found to migrate');
            return -1;
        }
        if (!property_exists($command->options, '--no-clear-db-cache')){
            DBCaches::Clear();
        }

        foreach ($c as $t) {
            $cl = get_class($t);
            Logger::info("migrate..." . $cl);
            if ($t->getCanInitDb()) {
                // call core migration - update 
                if ($t::migrate()) {
                    Logger::success("migrate:" . $cl);
                    if (!($t instanceof IGKModuleListMigration)){
                        $migHandle = new MigrationHandler($t);
                        $migHandle->up();
                    }
                } else {
                    Logger::danger("failed to migrate : " . $cl);
                }
            }
        } 
    }
}
