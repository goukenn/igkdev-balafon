<?php
// @author: C.A.D. BONDJE DOUE
// @filename: AppCommand.php
// @date: 20220803 13:48:57
// @desc: 



namespace IGK\System\Console;

use IGK\Controllers\BaseController;
use IGK\Helper\Activator;
use IGK\System\Console\AppCommandConstant;
use IGK\System\Console\AppCommandOptions;
use IGK\System\Console\Commands\BalafonCLICommand;
use IGK\System\Console\Commands\InitCommand;
use IGKException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\EnvironmentArrayException;
use ReflectionClass;
use ReflectionException;

require_once(__DIR__."/AppCommandConstant.php");

abstract class AppCommand {

    const ENV_KEY = "balafon/command_args";
    const OPTIONS_TAB_SPACE = "\r\t\t\t\t";
    const INIT_COMMAND_METHOD = 'InitCommand';
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

    /**
     * array of options
     * @var mixed
     */
    var $options;

    /**
     * define the command usage
     * @var mixed
     */
    var $usage;
    
    var $app;
    /**
     * show command usage
     * @param mixed $usage 
     * @return void 
     */
    protected function showCommandUsage(string $usage=null){
        Logger::print(sprintf("%s %s", App::Gets(App::AQUA, $this->command), $usage ?? $this->usage));
    }
    /**
     * register a command
     * @param mixed $command 
     * @param callable $callable 
     * @param string $desc 
     * @return void 
     * @throws EnvironmentArrayException 
     */
    public static function Register($command, callable $callable, $desc=""){
        $o = igk_createobj();
        $o->command = $command;
        $o->description = $desc;
        $o->callable = $callable;
        igk_push_env(self::ENV_KEY, $o);
    }
    /**
     * bind user - login with users
     * @param BaseController $controller 
     * @param int $id 
     * @return void 
     */
    public static function BindUser(BaseController $controller, int $id){
        if ($user = \IGK\Models\Users::Get('clId', $id)){
            $controller::login($user, null, false);
        }
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
                if ($cl == BalafonCLICommand::class){
                    continue;
                }
                if (is_subclass_of($cl, __CLASS__)){
                    if (!(igk_sys_reflect_class($cl))->isAbstract()){
                        // init command that contains Init
                        if (method_exists($cl, self::INIT_COMMAND_METHOD)){
                            call_user_func_array([$cl, self::INIT_COMMAND_METHOD],[]);
                        }   

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
                // - init module
                foreach($mod as $c => $v){
                    igk_require_module($c, null, false);
                }
                // - 
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
                            // if (file_exists(
                                $b = igk_io_expand_path($b);
                            //  )){
                                include($b);
                            // }else{
                            //     continue;
                            // }
                        }
                        if (!class_exists($ctrl)){
                            die("class [".$ctrl ."] not found or is abstract".$b);
                        }
                        if ((igk_sys_reflect_class($ctrl))->isAbstract() || !is_subclass_of($ctrl, __CLASS__))
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
        Logger::print("");
        $this->showOptions();
        Logger::print("");
    }
    /**
     * show command usage
     * @return void 
     */
    protected function showUsage(){
        if ($u = $this->usage){
            self::showCommandUsage($u);
        }
    }
    /**
     * show command options
     * @return void 
     */
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
            Logger::print( App::Gets(App::GREEN, $k). self::OPTIONS_TAB_SPACE. "{$v}". PHP_EOL); 
        }
        Logger::print("");
    }
    /**
     * generate file according to command information
     * @param mixed $command 
     * @param array $bind 
     * @param mixed $extra 
     * @return void 
     */
    public static function Generate($command, array $bind, ...$extra){    
        foreach($bind as $path=>$callback){
            if (!file_exists($path)){
                $callback($path, $command, ...$extra); 
            }
        }       
    }
    /**
     * retrieve command author
     * @param mixed $command 
     * @return mixed 
     */
    public function getAuthor($command){
        if (!$command->app){
            return IGK_AUTHOR;
        }
        return $command->app->getConfigs()->get("author", IGK_AUTHOR);
    }
    /**
     * helper create command options
     * @param mixed $command 
     * @return mixed 
     */
    public static function CreateOptionsCommandFrom($command, ?array $options=null){
        $c = (object)$command;
        unset($c->options);
        
        $tp = $options ?? [];
        $c->options = (object)$tp; 
        
        $g = Activator::CreateNewInstance(AppCommandOptions::class, $c);
        return $g;
    }

   
}