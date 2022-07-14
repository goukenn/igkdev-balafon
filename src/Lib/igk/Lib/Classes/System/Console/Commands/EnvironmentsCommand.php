<?php


namespace IGK\System\Console\Commands;

use IGK\Helper\SysUtils;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use function igk_resources_gets as __;

/** @package IGK\System\Console\Commands */
class EnvironmentsCommand extends AppExecCommand{
    var $command = "--environment";
    var $category = "sys";
    var $desc = "display environment setting";
    public function exec($command) { 
        Logger::info("Display environment settings");
        $env = [
            "environment" => igk_environment()->name()
        ];
        $env["config_file"] = igk_configs()->getConfigFile();
        Logger::print(str_repeat("=", 20));
        $app = $command->app;
        foreach($env as $k=>$v){
            Logger::print($k."=".$v);
        }

        Logger::print(str_repeat("=", 20));
        $e = igk_configs()->getEntries();
        ksort($e);
        foreach($e as $k=>$v){
            Logger::print($k."= ".igk_ob_get($v));
        }

    }

}