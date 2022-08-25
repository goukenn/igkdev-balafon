<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Scaffold.php
// @date: 20220622 19:06:59
// @desc: scaffold helper

namespace IGK\System\Console\Commands;

use IGK\System\Console\App;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Console\Scaffold\ActionScaffold;
use IGK\System\Exceptions\ArgumentNotValidException;

/**
 * 
 * @package IGK\System\Console\Commands
 */
class ScaffoldCommand extends AppExecCommand
{
    var $command = "--scaffold";

    var $category = "scaffold";

    var $desc  = "scaffold command line";

    static $sm_scaffold;

    var $options = [
    ];

    public static function RegisterScaffold(string $name, string $cl){
        if(empty($name)){
            throw new ArgumentNotValidException("name");
        }
        if (self::$sm_scaffold===null)
            self::$sm_scaffold = [];
        if (!empty($cl) && class_exists($cl)){
            self::$sm_scaffold[$name] = $cl;
        }
    }
    public function exec($command, string $cmd =null)
    {   
        $result = null;
        $failed = false;
        if (!is_null($cmd)){ 
            $scaffold_tab = array_merge(self::$sm_scaffold,
             igk_environment()->get("scoffold_commands", [])
            );
            
            if ($c = igk_getv($scaffold_tab, $cmd)){
                $m = new $c();
                $result = $m->exec($command, ...array_slice(func_get_args(), 2));
                $failed = true;
            }
        }
        if (!$failed)
        {
            $this->help();
            return false;
        }
        return $result;

    }
    public function help()
    {
        Logger::print("-");
        Logger::info("Scaffold command");
        Logger::print("-\n");
        Logger::print(
            "Usage : " . App::gets(App::GREEN, $this->command) . " ".
            App::gets(App::BLUE_I, "type") 
            . " [options]"
        );
        Logger::print("\n");

        Logger::print("list of registrated types");
        
        Logger::print("\n");


        array_map(function($a){
            Logger::print("\t".$a);
        }, array_keys(self::$sm_scaffold));
        Logger::print("\n\n");
        $this->showOptions();
    }
}

ScaffoldCommand::RegisterScaffold("action", ActionScaffold::class);
