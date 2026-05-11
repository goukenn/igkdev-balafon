<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ResetDbCommand.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Console\Commands;
use IGK\Controllers\BaseController; 
use IGK\Controllers\SysDbController;  
use IGK\Helper\Database;
use IGK\System\Caches\DBCaches; 
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger; 
use IGK\System\Delegates\InvocatorListDelegate;
use IGKModuleListMigration;
use Illuminate\Database\Console\Seeds\SeedCommand;

/**
* auto generate doc.
* @package IGK\System\Console\Commands
*/
class ResetDbCommand extends AppExecCommand
{
    /**
    * Property: command.
    * @var mixed
    */
    var $command = "--db:resetdb";
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = "reset database";
    /**
    * Property: category.
    * @var mixed
    */
    var $category = "db";
    /**
    * Property: options.
    * @var mixed
    */
    var $options = [
        "--force" => "flag: force class generation",
        "--clean"=>"flag: clean model output directory",
        "--seed" => "flag: do seed",
        "--querydebug" => "flag: activate query debug",
        "--controller:controller_name" => "set controller"
    ];
    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = '[controller] [db-command-options] [options]';
    /**
    * Exec.
    * @param mixed $command
    * @param null|string $ctrl
    */
    public function exec($command, ?string $ctrl = null)
    {
        DbCommandHelper::Init($command);
        $seed =  property_exists($command->options, "--seed");
        $force = property_exists($command->options, "--force");
        $clean = property_exists($command->options, "--clean");
        if ($ctrl== self::SYS_CTRL_PLACEHOLDER){
            $ctrl = SysDbController::ctrl();
        }
        $ctrl =  $ctrl ?? igk_getv($command->options, "--controller");
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
            $this->globalResetDatabase($force, $seed, $clean);
            return; 
        }
        if (!$c)
         return -1;
        $this->controllerResetDatabase($c, $force, $seed, $clean);
        Logger::print("-");        
        if ($seed){  
            $ad = SysDbController::ctrl()->getDataAdapter();
            if ($ad->connect()){
                DbCommandHelper::Seed($ctrl);
                $ad->close();
            }
        }
        Logger::success("resetdb - done");
        return 1;
    }
    /**
    * reset controller
    * @param array<BaseController> $c
    * @param bool $force
    * @param bool $seed
    * @param bool $clean
    * @return void
    */
    public function controllerResetDatabase($c, bool $force, bool $seed=false, bool $clean=false){
        foreach ($c as $m) {
            $n = get_class($m);
            if ($m->getCanInitDb()) {
                $m->register_autoload();
                Logger::print("resetdb : " . $n);
                if ( ($m->resetDb(false, $force, $clean)) !=1){                
                    Logger::danger("failed resetdb [".$n."]");                    
                } else {
                    Logger::success("complete: [".$n."]");
                    $droped[] = $m;
                    DBCaches::Update($m, true);
                }
            } else {
                Logger::warn(sprintf("can't reset database for %s", get_class($m)));
            }
        }
    }
    /**
    * Global reset database.
    * @param bool $force
    * @param bool $seed
    * @param bool $clean
    * @return bool
    */
    public function globalResetDatabase(bool $force, bool $seed=false, bool $clean =false):bool{
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
                function(BaseController $b, $func, $arguments){
                    $cl = get_class($b);
                    Logger::print("$func : ".$cl);
                    return call_user_func_array([$cl, $func], $arguments);
                }
        );
        igk_environment()->NO_DB_LOG = 1;
        // + | --------------------------------------------------------------------
        // + | 1. downgrade - 
        // + |
        // + | at init migrations of modules can be empty start migration 
        $migrations && $migrations::downgrade();
        $projects::dropDb(false, true);
        $sysdb::dropDb(false, true, $clean);
        // + | --------------------------------------------------------------------
        // + | 2. upgrade
        // + |
        DBCaches::Clear();
        Database::InitSystemDb();
        // + | --------------------------------------------------------------------
        // + | JUST STORE CACHE
        // + |
        if ($seed && ($force || igk_environment()->isDev())){
            Logger::print('seeding db not implement');
        }
        Logger::success("resetdb - all - complete"); 
        igk_environment()->NO_DB_LOG = null;
        return true;
    }
    /**
     * seed controler
     * @param mixed $command 
     * @return void 
     */
    public function seedController($command){ 
        $seed = $command->app->command["--db:seed"];
        $fc = $seed["0"];
        $fc("resetdb", $command);    
    }
}