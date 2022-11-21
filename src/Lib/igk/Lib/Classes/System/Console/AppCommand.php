<?php
// @author: C.A.D. BONDJE DOUE
// @filename: AppCommand.php
// @date: 20220803 13:48:57
// @desc: 



namespace IGK\System\Console;
use IGK\System\Console\AppCommandConstant;
use IGK\System\Console\Commands\InitCommand;
use IGKException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\EnvironmentArrayException;
use ReflectionClass;
use ReflectionException;

require_once(__DIR__."/AppCommandConstant.php");

abstract class AppCommand {

    const ENV_KEY = "balafon/command_args";

    /**
     * register command name
     * @var mixed
     */
    var $command;

    /**
     * register callable
     * @var mixed
     */
    var $callable;

    /**
     * register description
     * @var mixed
     */
    var $desc;

    /**
     * define the command category
     * @var mixed
     */
    var $category;

    var $options;
    
    public static function Register($command, callable $callable, $desc=""){
        $o = igk_createobj();
        $o->command = $command;
        $o->description = $desc;
        $o->callable = $callable;
        igk_push_env(self::ENV_KEY, $o);
    }
    /**
     * get store command
     * @return array 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws EnvironmentArrayException 
     */
    public static function GetCommands(){
        static $loaded_command = null;
        if ($loaded_command === null){

            $loaded_command = [];

            foreach(get_declared_classes() as $cl){
                if (is_subclass_of($cl, __CLASS__)){
                    if (!(igk_sys_reflect_class($cl))->isAbstract()){
                        $b = new $cl();
                        if (empty($b->command)){
                            die("command : ".$cl. " not specified");
                        } 
                        $loaded_command[$b->command] = $b; 
                    }
                }
            }
            if (file_exists($file = AppCommandConstant::GetCacheFile())){
                
                $list = include($file);
                $mod = igk_get_modules();
                //init module
                foreach($mod as $c => $v){
                    igk_require_module($c, null, false);
                }
                //
                foreach($list as $ctrl=>$b){
                    if (!is_string($b) && ($m = igk_getctrl($ctrl, false))){
                        $m::register_autoload();
                        foreach($b as $c){
                            $b = new $c();
                            if (empty($b->command)){
                                die("command : ".$c. " not specified");
                            } 
                            $loaded_command[$b->command] = $b; 
                        }
                    }else  {
                        if (!class_exists($ctrl,false)){
                            $b = igk_io_expand_path($b);
                            include($b);
                        }
                        if (!class_exists($ctrl)){
                            die("class [".$ctrl ."] not found or is abstract".$b);
                        }
                        if ((igk_sys_reflect_class($ctrl))->isAbstract())
                            continue; 
                        $b = new $ctrl();
                        $loaded_command[$b->command] = $b;  
                    }
                }
            }
            else {
                // init command before 
                // $init_command = new InitCommand;
                // $cmd = null; // self::Create("");
                // $init_command->exec($app);
                Logger::info("command file not present ". $file);
                Logger::info("please run with  --command:init to initialize the file ");
            }
        } 
        return  array_merge($loaded_command,  igk_environment()->get(self::ENV_KEY, [])); 
    }
    /**
     * create command argument
     * @return static 
     */
    public static function Create(string $args){
        $pb = null;// new self;

        return $pb;
    }
    /**
     * execute command
     * @param mixed $args 
     * @param mixed $command 
     * @return mixed 
     */
    public function run($args, $command){
        if ($fc = $this->callable){
            $argument = func_get_args();
            return $fc(...$argument);
        }
    }
    /**
     * help view
     * @return void 
     */
    public function help(){
        Logger::print("");        
        Logger::info($this->command. PHP_EOL);
        if ($d = $this->desc){
            Logger::print($d. PHP_EOL); 
        }
        $this->showUsage();
        $this->showOptions();
        Logger::print("");
    }
    protected function showUsage(){

    }
    protected function showOptions(){
        $opts = $this->options ;
        if (!$opts){
            return ;
        }
        Logger::info("Options");
        foreach($opts as $k=>$v){
            if (empty($v) && (strpos($k, '+')===0)){
                Logger::print(App::Gets(App::AQUA, $k));     
                Logger::print('');
                continue;
            }
            Logger::print( App::Gets(App::GREEN, $k). Logger::TabSpace. " {$v}". PHP_EOL); 
        }
        Logger::print("");
    }
    public static function Generate($command, array $bind, ...$extra){
        
            foreach($bind as $path=>$callback){
                if (!file_exists($path)){
                    $callback($path, $command, ...$extra); 
                }
            }
        
    }
}