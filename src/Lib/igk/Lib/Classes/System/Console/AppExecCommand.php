<?php
// @author: C.A.D. BONDJE DOUE
// @filename: AppExecCommand.php
// @date: 20220803 13:48:57
// @desc: 


namespace  IGK\System\Console;

use IGK\System\Console\Commands\DbCommandHelper;
use IGKControllerManagerObject;
use IGKException;

abstract class AppExecCommand extends AppCommand{
    /**
     * get option values
     * @param mixed $command 
     * @param string $list 
     * @return mixed 
     * @throws IGKException 
     */
    protected static function GetOptions($command, string $list){        
        foreach(explode("|", $list) as $m){
            if ($m = igk_getv($command->options,$m)){
                return $m;
            }
        }
    }
    /**
     * check if has options set in command
     * @param mixed $command 
     * @param string $list 
     * @return true|void 
     */
    protected static function GetHasOptions($command, string $list){        
        foreach(explode("|", $list) as $m){
            if (property_exists($command->options, $m)){
                return true;
            }
        }
    }

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
                DbCommandHelper::Init($command); 
                $fc = $this->handle;
                $args = func_get_args();
                return $fc(...$args);

            };
        }
    }
    public abstract function exec($command);
}