<?php

namespace  IGK\System\Console;

use IGK\System\Console\Commands\DbCommand;
use IGKControllerManagerObject;

abstract class AppExecCommand extends AppCommand{
    public function __construct()
    {
        $this->handle = [$this, "exec"];
    }
    public function run($args, $command)
    {
        if ($this->handle){

            $command->exec = function($command){
                if (property_exists($command->options, "--help")){
                    $h= $this->help();
                    Logger::print("\n");
                    return $h;
                }
                DbCommand::Init($command);
                igk_hook("init_app", [IGKControllerManagerObject::getInstance()]);
                $fc = $this->handle;
                $args = func_get_args();
                return $fc(...$args);

            };
        }
    }
    public abstract function exec($command);
}