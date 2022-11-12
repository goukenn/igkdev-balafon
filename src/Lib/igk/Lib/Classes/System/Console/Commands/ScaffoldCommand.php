<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Scaffold.php
// @date: 20220622 19:06:59
// @desc: scaffold helper

namespace IGK\System\Console\Commands;

use IGK\Helper\IO;
use IGK\System\Console\App;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Console\Scaffold\ActionScaffold;
use IGK\System\Console\Scaffold\AuthScaffold;
use IGK\System\Exceptions\ArgumentNotValidException;
use ReflectionClass;

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
    private static function _InitScaffOfld(){
        $ns = \IGK\System\Console\Scaffold::class;
        foreach(IO::GetFiles(IGK_LIB_CLASSES_DIR."/System/Console/Scaffold", "/\.php$/") as $f){
            $clName = igk_io_basenamewithoutext(basename($f));
            require_once($f);
            if ($clName == \ScaffoldBase::class){
                continue;
            }
            $tc = $ns ."\\".$clName;
            if ((new ReflectionClass($tc))->isAbstract()){
                continue;
            }
            if (strstr($clName, "Scaffold") == 'Scaffold'){
                $name = strtolower(substr($clName, 0, -strlen("Scaffold")));
                self::$sm_scaffold[$name] = $tc;
            }else {
                self::$sm_scaffold[strtolower($clname)] = $tc;
            }
        }
        
    }
    public static function RegisterScaffold(string $name, string $cl){
        if(empty($name)){
            throw new ArgumentNotValidException("name");
        }
        if (self::$sm_scaffold===null){
            self::$sm_scaffold = [];
            self::_InitScaffOfld();
        }
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
    public function help(...$args)
    {
        Logger::print("-");
        Logger::info("Scaffold command");
        Logger::print("-");
        $scaffold_tab = array_merge(self::$sm_scaffold,
         igk_environment()->get("scaffold_commands", [])
        );
        if ($args){
            $action = $args[0];
            if ($c = igk_getv($scaffold_tab, $action)){
                $m = new $c();
                Logger::print(
                    "Usage : " . App::gets(App::GREEN, $this->command) . " ".
                    App::gets(App::BLUE_I, $action) 
                    . " [options]"
                ); 
                Logger::print('');
                $m->showHelp($this->command);
                Logger::print('');
                return;
            }
        }
        Logger::print(
            "Usage : " . App::gets(App::GREEN, $this->command) . " ".
            App::gets(App::BLUE_I, "type") 
            . " [options]"
        ); 
    
        Logger::print("");
        Logger::print("list of registrated types");        
        Logger::print("");


        array_map(function($a)use($scaffold_tab){
            $cl = $scaffold_tab[$a];
            $m = new $cl();
            Logger::print(implode("", 
                ["\t".$a,
                "\r\t\t\t".$m->description
            ]));
        }, array_keys(self::$sm_scaffold));
        Logger::print("\n\n");
        $this->showOptions();
    }
}

ScaffoldCommand::RegisterScaffold("action", ActionScaffold::class);
// ScaffoldCommand::RegisterScaffold("auth", AuthScaffold::class);
