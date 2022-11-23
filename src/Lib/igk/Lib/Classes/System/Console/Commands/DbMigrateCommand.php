<?php

// @author: C.A.D. BONDJE DOUE
// @filename: DbMigrateCommand.php
// @date: 20221111 22:30:40
// @desc: 

use IGK\Controllers\ControllerExtension;
use IGK\Controllers\SysDbController;
use IGK\Helper\SysUtils;
use IGK\System\Caches\DBCaches;
use IGK\System\Caches\DBCachesModelInitializer;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Commands\DbCommandHelper;
use IGK\System\Console\Logger;
use IGK\System\Database\MigrationHandler;

class DbMigrateCommand extends AppExecCommand
{

    var $command = '--db:migrate';

    var $category = 'db';

    var $description = 'migration command';

    public function help()
    {
        Logger::info('migrate utility command');
    }

    public function exec($command, $ctrl = null)
    {
        DbCommandHelper::Init($command);
        
        if (!is_null($ctrl) && ($c = igk_getctrl($ctrl, false))) {
            $c = [$c];
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
        foreach ($c as $t) {
            $cl = get_class($t);
            Logger::info("migrate..." . $cl);
            if ($t->getCanInitDb()) {
                if ($t::migrate()) {
                    Logger::success("migrate:" . $cl);
                    $migHandle = new MigrationHandler($t);
                    $migHandle->up(); 
                } else {
                    Logger::danger("failed to migrate : " . $cl);
                }
            }
        }
        $s = \IGK\Models\Migrations::AddIfNotExists('migration_'.date('Ymd'), 1);  
        // update all model files 
        DBCaches::Reset();
        $data = DBCaches::GetCacheData();
        $initalizer = DBCachesModelInitializer::Init($data);

        $initalizer->bootStrap(true);
 


    }
}
