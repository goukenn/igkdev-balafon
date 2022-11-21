<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ResetDbCommand.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\System\Console\Commands;

use IGK\Controllers\BaseController;
use IGK\Controllers\RootControllerBase;
use IGK\Controllers\SysDbController;
use IGK\Database\DbSchemaDefinitions;
use IGK\Database\DbSchemas;
use IGK\Helper\Database;
use IGK\System\Caches\DBCaches;
use IGK\System\Caches\EnvControllerCacheDataBase;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Database\DbUtils;
use IGK\System\Delegates\InvocatorListDelegate;
use IGKModuleListMigration;
use Illuminate\Database\Console\DbCommand;

class ResetDbCommand extends AppExecCommand
{
    var $command = "--db:resetdb";
    var $desc = "reset database";
    var $category = "db";

    var $options = [
        "--force" => "flag: force class generation",
        "--seed" => "flag: do seed",
        "--querydebug" => "flag: activate query debug"
    ];

    public function exec($command, $ctrl = null)
    {
        DbCommandHelper::Init($command);
        $seed = property_exists($command->options, "--seed");
        $force = property_exists($command->options, "--force");
        $use_core_db = false;
        $sysdb = SysDbController::ctrl();
     
        // igk_dev_wln("db: ". igk_configs()->db_name, "server: ".igk_configs()->db_server); 
        if ($ctrl) {
            $c = \IGK\Helper\SysUtils::GetControllerByName($ctrl);

            if ($c) {
                $c = [$c];
            } else {
                Logger::danger(sprintf("controller [%s] not found", $ctrl));
                return -1;
            }
        } else {
            // + | --------------------------------------------------------------------
            // + | globally reset command
            // + |
            $this->globalResetDatabase($force, false, $seed);
            return; 
        }
        if (!$c)
         return -1;
         
        $this->controllerResetDatabase($c, $force, $seed);
      
        // init modules controller 
        Logger::print("-");        
        if ($seed) {
            $fc = $command->exec;
            $fc($command, $ctrl);
        }
        Logger::success("Done");
        return 1;
    }
    /**
     * 
     * @param array<BaseController> $c 
     * @param bool $force 
     * @return void 
     */
    public function controllerResetDatabase($c, bool $force, bool $seed){
        foreach ($c as $m) {
            $n = get_class($m);
            if ($m->getCanInitDb()) {
                $m->register_autoload();
                Logger::print("resetdb : " . $n);
                if ( ($m->resetDb(false, $force)) !=1){                
                    Logger::danger("failed resetdb [".$n."]");                    
                } else {
                    Logger::success("complete: [".$n."]");
                    $droped[] = $m;
                }
            }
        }
    }
    public function globalResetDatabase(bool $force, bool $seed=false):bool{
     
        $migrations = IGKModuleListMigration::CreateModulesMigration();
        $sysdb = SysDbController::ctrl();
        $sysdb_adapter = $sysdb->getDataAdapterName();
        $projects = InvocatorListDelegate::Create(array_filter(array_map(function($a)use($sysdb, $sysdb_adapter){
                    if (($sysdb==$a) || !$a->getCanInitDb() || ($a->getDataAdapterName() != $sysdb_adapter))
                        return null;
                    $a::register_autoload();
                    return $a;
                }, 
                igk_app()->getControllerManager()->getControllers())), 
                function($b, $func, $arguments){
                    Logger::print("$func : ".get_class($b));
                    return call_user_func_array([get_class($b), $func], $arguments);
                }
        );

        EnvControllerCacheDataBase::ResetCache();
     
        // + | --------------------------------------------------------------------
        // + | 1. downgrade 
        // + |
        $migrations::downgrade();

        $projects::dropDb(false, true);

        $sysdb::dropDb(false, true);

        // + | --------------------------------------------------------------------
        // + | 2. upgrade
        // + |
        DbSchemaDefinitions::ResetCache();   
        DBCaches::Clear();
        Database::InitSystemDb();
        // $sysdb::resetDb(false, true);
        
        // $projects::resetDb(false, $force);   

        // $migrations::migrate(); 

        // + | --------------------------------------------------------------------
        // + | JUST STORE CACHE
        // + |
          
        if ($seed && ($force || igk_environment()->isDev())){
            Logger::print('seeding db not implement');
        }
        Logger::success("resetdb - all - complete"); 
        return true;
    }
    public function seedController(){
        $commands = $this->GetCommands();
        // if ($seed) {
        //     $seed = $command->app->command["--db:seed"];
        //     $fc = $seed["0"];
        //     $fc("resetdb", $command);
        // }
    }
}
