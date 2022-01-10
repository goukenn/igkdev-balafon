<?php

namespace IGK\System\Console;

use IGK\System\Configuration\XPathConfig;
use Closure;
use Exception;
use IGK\Helper\IO;
use IGK\System\Console\Commands\DbCommand;
use IGKApp;
use IGKAppType;
use IGKControllerManagerObject;
use IGK\Helper\IO as IGKIO;
use IGKXmlNode;
use stdClass;
use Throwable;

// igk_load_class(\IGKHtmlChildElementCollections::class);
// igk_load_class(\HtmlUtils::class);
// igk_load_class(\IGK\Database\DbQueryDriver::class);
// igk_load_class(\IGKDataAdapter::class);
// igk_load_class(\SQLDataAdapter::class ); 
// igk_load_class(\IGK\System\Console\AppCommand::class);
// igk_load_class(\IGK\System\Console\AppExecCommand::class);
// igk_load_class(\IGK\System\Configuration\XPathConfig::class);
// igk_load_class(\IGKDbUtility::class);
// igk_load_class(\IGK\System\Configuration\ConfigUtils::class);
// igk_load_class(\IGKCSVDataAdapter::class);
// require_once(IGK_LIB_CLASSES_DIR."/IGKXmlChilds.php");
// require_once(IGK_LIB_CLASSES_DIR."/System/Database/MySQL/Controllers/IGKMySQLDataCtrl.php");
 
///<summary>represent Balafon CLI console Application</summary>
class App{
    const GREEN = "\e[1;32m";
    const YELLOW = "\e[0;33m";
    const YELLOW_B = "\e[1;33m";
    const YELLOW_I = "\e[3;33m";
    const RED = "\e[1;31m";
    const BLUE = "\e[0;34m";
    const BLUE_B = "\e[1;34m";
    const BLUE_I = "\e[3;34m";
    const PURPLE = "\e[3;35m";
    const AQUA = "\e[3;36m";
    const END = "\e[0m";
    /**
     * application version
     * @var string
     */
    public $version = "0.1.0";
    /**
     * available command
     * @var mixed
     */
    public $command = [];
    /**
     * setup the base command
     * @var mixed
     */
    protected $basePath;

    /**
     * store application configuration
     */
    protected $configs;

    public function getConfigs(){
        return $this->configs;
    }
    /**
     * run application command line
     * @param array $command default commands
     * @param string $basePath base path
     * @param XPathConfig $configs loaded configuration
     * @return void 
     * @throws Exception 
     */
    public static function Run($command=[], string $basePath=null, XPathConfig $configs=null){ 
        $app = (new static);
        if ($basePath === null){
            $basePath = getcwd();
        }
  
        $wdir = sys_get_temp_dir()."/balafon";
        IO::CreateDir($wdir);
        igk_environment()->set("app_type", IGKAppType::balafon);
        igk_environment()->set("workingdir", $wdir); 
        $app->basePath = $basePath;
        $app->configs = $configs;
        Logger::SetLogger(new ConsoleLogger($app));        
        $app->boot();

        $command_args = AppCommand::GetCommands();

        if ($command_args){ 
            foreach($command_args as $c){                 
                $callbable = null;
                if ($c instanceof AppCommand ){
                    $callbable = [$c, "run"];
                } else {
                    $callbable = $c->callable;
                }
                $command[$c->command] = [
                    $callbable,
                    $c->desc,
                    $c->category
                ]; 
            }
        } 
        $handle = [];
        foreach($command as $n=>$b){
            if(count($c = explode(",", $n))>1){
                array_map(function($i)use(& $handle, $b){
                    $handle[trim($i)] = $b; 
                }, $c);
            }else {
                $handle[trim($n)] = $b;
            }
        }
        ksort($command);
        $app->command = $command;        
        $tab = array_slice(igk_server()->argv, 1);
        return self::Exec($app, $tab); 
    }

    public static function Exec(App $app, array $args){
        $command = $app->command;
        $cnf = $app->getConfigs();
        $app = new static();  
        $app->configs = $cnf;

        if ($command_args = AppCommand::GetCommands()){ 
            foreach($command_args as $c){                
                $callbable = null;
                if ($c instanceof AppCommand ){
                    $callbable = [$c, "run"];
                } else {
                    $callbable = $c->callable;
                }
                $command[$c->command] = [
                    $callbable,
                    $c->desc,
                    $c->category
                ];
            }
        }

        $handle = [];
        foreach($command as $n=>$b){
            if(count($c = explode(",", $n))>1){
                array_map(function($i)use(& $handle, $b){
                    $handle[trim($i)] = $b; 
                }, $c);
            }else {
                $handle[trim($n)] = $b;
            }
        }
        ksort($command);
        $app->command = $command;
        
        $tab = $args;  
        $command = igk_createobj();
        $command->app = $app;
        $command->command = $tab;
        $command->exec = null;
        $command->storage = array();  
        $command->waitForNextEntryFlag = false;
        $command->options = new stdClass();
        $action = null;
        $args = [];
        $show_help = true;

        foreach($tab as $v){
             
            if ($command->waitForNextEntryFlag){
                $action($v, $command, []);
                $command->waitForNextEntryFlag = false;
            }
            if ( isset($handle[$v]) ){
                $action = is_callable($handle[$v])?$handle[$v]: $handle[$v][0];
                $action($v, $command, implode(":", array_slice($c,1)));
            }
            else { 
                $c = explode(":", $v);
                $v_ts =  implode(":", array_slice($c,1));
                if (isset($handle[$c[0]]))
                {
                    if (isset($handle[$v])){
                        $action = is_callable($handle[$v])?$handle[$v]: $handle[$v][0];
                        $action($v, $command, $v_ts);
                    }
                }else {

                    if ($c[0][0]=="-"){ 
                        if (!property_exists($command->options, $c[0])){
                            $command->options->{$c[0]} = $v_ts;
                        }else{
                            if (!is_array($command->options->{$c[0]})){
                                $command->options->{$c[0]} = [$command->options->{$c[0]}];
                            }
                            $command->options->{$c[0]}[] = $v_ts;
                        }
                    }
                    else
                        $args[] = $v;
                }
            }
        }

        try{
            $action = $command->exec; //($v, $command, implode(":", array_slice($c,1)));
            if ($action){
                if (property_exists($command->options, "--help")){  
                    $command->app->showHelp($command->command[0]);
                    return 0;
                }
                return $action($command , ...$args); 
            }else{
                Logger::danger("no action found");
            }
        } catch (Exception $ex){
            $app->print(self::gets(self::RED, "BALAFON Error : "). $ex->getMessage());
            igk_show_exception_trace($ex->getTrace(), 0);
            igk_exit();
        }
        catch (Throwable $ex){
            Logger::danger("error: throw: ".$ex->getMessage());
            Logger::print($ex->getFile().":".$ex->getLine());
            igk_show_exception_trace($ex->getTrace(), 0);
            $show_help = false;
        }
        if ($show_help)
        $app->showHelp(); 
    }
    protected function boot(){       
        igk_hook("console::app_boot", $this); 
    }
    public function print(...$text){
        foreach($text as $s){ 
            echo $s. PHP_EOL;
        }
    }
    public function print_debug(...$text){    
        if (igk_is_debug())
            $this->print(...$text); 
    }
    public function showHelp($command=null){
        if (!empty($command) && ($cmd = $this->command[$command])){
            if (is_array($inf = igk_getv($cmd, 1))){
                $cf = $inf["help"];
                if ( $cf instanceof Closure){
                    $cf();
                }else {
                    igk_wln($cf);
                }
            }else {
                if ($cmd[0] instanceof Closure){
                    if (is_array($m = igk_getv($cmd, 1)) && is_callable($fc = igk_getv($m, "help"))){
                        $fc();
                    }  
                }else{
                    if (isset($cmd[0][0])){
                        $cmd[0][0]->help();
                    }
                }
            }
            return;
        }
        $this->print("BALAFON CLI-UTILITY");;
        $this->print("Author: C.A.D. BONDJE DOUE");
        $this->print(sprintf("Version:  %s", self::gets(self::GREEN, $this->version)));
        $this->print(""); 
        $this->print(self::gets(self::YELLOW, "Usage:"));
        $this->print("\tbalafon [command] [options] [arguments]");
        $this->print("");
        $this->print("");

        $groups = [];
        array_walk($this->command, function($c,$key)use(& $groups){
            
            $cat = igk_getv($c, 2, "");
             
            if (!isset($groups[$cat]))
                $groups[$cat] = [];
            $groups[$cat][$key] = $c;
        } );

        ksort($groups,  SORT_FLAG_CASE | SORT_STRING );  
        $key=key($groups);
        while((count($groups)>0) && ( $g = array_shift($groups))){
            if (!empty($key)){
                Logger::info("groups: ".$key);
                Logger::print("");
            }

        foreach($g as $n=>$c){
                $s = " ".self::GREEN.$n."\e[0m \r\t\t\t\t";
            
                if (is_array($c) && is_array($c[1])){
                    $s .= (igk_getv($c[1], "desc"));
                } 
                else  if (! ($c instanceof Closure)){
                    $s.= (igk_getv($c, 1));
                }

                $this->print($s."\n");
            }
            $key=key($groups);
        }
        $this->print("");
    }
    public function getLogFolder(){
        if($this->configs){
            return $this->configs->get("logFolder");
        }
    }
    public static function gets($color, $s){
        return $color.$s."\e[0m";
    }

    private function __construct()
    { 
    }

    
}