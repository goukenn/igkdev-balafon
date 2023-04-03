<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Logger.php
// @date: 20220426 10:28:49
// @desc: Core Logger - use in console


namespace IGK\System\Console;

/**
 * 
 * @package IGK\System\Console
 * @method static void danger(string $message)  
 * @method static void print(string $message)  
 * @method static void info(string $message)  
 */
class Logger{
    static $sm_logger;

    const TabSpace = "\r\t\t\t\t";
    
    public static function SetLogger($logger){
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
        if (self::$sm_logger){
            return self::$sm_logger->$name(...$arguments);
        }
    }
}