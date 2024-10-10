<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Logger.php
// @date: 20220426 10:28:49
// @desc: Core Logger - use in console


namespace IGK\System\Console;

use Exception;

/**
 * use to write logger in console data
 * @package IGK\System\Console
 * @method static void danger(string $message) - error message
 * @method static void success(string $message) - success message
 * @method static void warn(string $message) - warning message
 * @method static void info(string $message) - information message
 * @method static void print(string $message)  - just print
 */
class Logger{
    /**
     * IConsoleLogger
     * @var mixed
     */
    private static $sm_logger;
    private static $sm_colorizer;

    const TabSpace = "\r\t\t\t\t";
    
    /**
     * 
     * @param mixed $logger logger object - iconsole logger object
     * @return void 
     */
    public static function SetLogger(?IConsoleLogger $logger){
        self::$sm_logger = $logger;
    }
    public static function SetColorizer(?Colorize $cl){
        self::$sm_colorizer = $cl;
    }
    public static function GetColorizer(){
        return self::$sm_colorizer;
    }
    /**
     * print message
     * @param mixed $args 
     * @param mixed $tabspace 
     * @return void 
     */
    public static function printr($args, $tabspace=null){
        if (is_array($args)){
            $tabspace = $tabspace ?? self::TabSpace;
            foreach($args as $k=>$v){
                self::print($k.$tabspace.$v);
            }
        }
    }  
    /**
     * 
     * @param mixed $name 
     * @param mixed $arguments 
     * @return void|mixed 
     * @throws Exception 
     */
    public static function __callStatic($name, $arguments)
    {
        if (($name!='print') && igk_environment()->NoConsoleLogger){
            return;
        }
        if (!in_array($name, ['log', 'warning','success','danger','print', 'info','warn', 'printf', 'offscreen'])){           
            igk_die($name . " - log not in a logger list allowed method ");
        }

        if ($name==='offscreen'){
            if (self::$sm_logger){
                return self::$sm_logger->offscreen();
            }
            return null;
        }
        if (self::$sm_logger){       
            $l = Logger::GetColorizer();
            if ($name!='print'){
                Logger::SetColorizer(null); 
                self::$sm_logger->offscreen()->$name(...$arguments);
            }
            else{
                self::$sm_logger->$name(...$arguments);
            }      
            Logger::SetColorizer($l);
        }
    }
}