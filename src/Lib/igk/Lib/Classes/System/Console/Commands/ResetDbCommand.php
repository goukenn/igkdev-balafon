<?php
namespace IGK\System\Console\Commands;

use IGK\Controllers\RootControllerBase;
use IGK\Controllers\SysDbController;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Database\DbUtils;
use IGKModuleListMigration;

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
        $seed = property_exists($command->options, "--seed");
        $force = property_exists($command->options, "--force");
         
        if ($seed){
            $seed = $command->app->command["--db:seed"];
            $fc = $seed["0"];
            $fc("resetdb", $command); 
        }

        if ($ctrl){
            if ($c = igk_getctrl($ctrl, false)){            
                $c = [$c];
            } else{
                Logger::danger("controller not found");
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
                if ($m->getCanInitDb()){
                    $m->register_autoload();
                    $command->app->print("resetdb : " . get_class($m));
                    if ($m::resetDb(false, $force)!=0){
                        Logger::danger("failed");
                    } else {
                        Logger::success("complete: ".get_class($m));
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