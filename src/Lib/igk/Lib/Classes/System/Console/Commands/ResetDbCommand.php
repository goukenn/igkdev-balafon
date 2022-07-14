<?php
namespace IGK\System\Console\Commands;

use IGK\Controllers\BaseController;
use IGK\Controllers\RootControllerBase;
use IGK\Controllers\SysDbController;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Database\DbUtils;
use IGKModuleListMigration;
use Illuminate\Database\Console\DbCommand;

class ResetDbCommand extends AppExecCommand{
    var $command = "--db:resetdb";
    var $desc = "reset database"; 
    var $category = "db";

    var $options =[
        "--force"=>"flag: force class generation",
        "--seed" => "flag: do seed",
        "--querydebug"=>"flag: activate query debug"
    ];

    public function exec($command, $ctrl=null)
    {   
        DbCommandHelper::Init($command);
        $seed = property_exists($command->options, "--seed");
        $force = property_exists($command->options, "--force");
         
        if ($seed){
            $seed = $command->app->command["--db:seed"];
            $fc = $seed["0"];
            $fc("resetdb", $command); 
        }
       
        igk_wln("ok", igk_is_debug(), igk_environment()->querydebug);

        if ($ctrl){
            $c = \IGK\Helper\SysUtils::GetControllerByName($ctrl); 

            if ($c){            
                $c = [$c];
            } else{ 
                Logger::danger(sprintf("controller [%s] not found", $ctrl));
                return -1;
            }
        } else {
            $c = igk_app()->getControllerManager()->getControllers(); 

            usort($c, DbUtils::OrderController);
            if ($b = IGKModuleListMigration::CreateModulesMigration()){
                $c =  array_merge($c, [$b]); 
            } 
        }
        if ($c) {
            foreach ($c as $m) {
                $n = get_class($m);
                if ($m->getCanInitDb()){
                    $m->register_autoload();
                    $command->app->print("resetdb : " . $n);
                    if ( ($g = $m::resetDb(false, $force)) !=1){
                        // igk_wln_e($g);
                        Logger::danger("failed resetdb [".$n."]");
                    } else {
                        Logger::success("complete: [".$n."]");
                    }
                }
            }
            // init modules controller 


            Logger::print("-"); 
            if ($seed){
                $fc = $command->exec;
                $fc($command, $ctrl);
            }
            return 1;
        }
        return -1;
    }
}