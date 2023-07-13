<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Logger.php
// @date: 20220426 10:28:49
// @desc: Core Logger - use in console


namespace IGK\System\Console;

/**
 * use to write logger in console data
 * @package IGK\System\Console
 * @method static void danger(string $message)  
 * @method static void print(string $message)  
 * @method static void info(string $message)  
 */
class Logger{
    /**
     * IConsoleLogger
     * @var mixed
     */
    static $sm_logger;

    const TabSpace = "\r\t\t\t\t";
    
    /**
     * 
     * @param mixed $logger logger object - iconsole logger object
     * @return void 
     */
    public static function SetLogger(?IConsoleLogger $logger){
        self::$sm_logger = $logger;
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

    public static function __callStatic($name, $arguments)
    {
        if (($name!='print') && igk_environment()->NoConsoleLogger){
            return;
        }
        if (!in_array($name, ['log', 'warning','success','danger','print', 'info','warn'])){           
            igk_die($name . " - log not in a logger list allowed method ");
        }
        if (self::$sm_logger){            
            return self::$sm_logger->$name(...$arguments);
        }
    }
}