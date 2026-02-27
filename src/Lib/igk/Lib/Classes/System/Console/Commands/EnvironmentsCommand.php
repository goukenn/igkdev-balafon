<?php
// @author: C.A.D. BONDJE DOUE
// @filename: EnvironmentsCommand.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Console\Commands;
use IGK\Helper\SysUtils;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use function igk_resources_gets as __;

/**
* auto generate doc.
* @package IGK\System\Console\Commands
*/
class EnvironmentsCommand extends AppExecCommand{

    /**
    * Property: command.
    * @var mixed
    */
    var $command = "--environment";

    /**
    * Property: category.
    * @var mixed
    */
    var $category = 'sys';

    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = "display environment setting";

    /**
    * Exec.
    * @param mixed $command
    */
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