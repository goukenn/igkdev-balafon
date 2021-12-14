<?php

namespace IGK\System\Console;

class Logger{
    static $sm_logger;

    const TabSpace = "\r\t\t\t";
    
    public static function SetLogger($logger){
        self::$sm_logger = $logger;
    }

    public static function __callStatic($name, $arguments)
    {
        if (self::$sm_logger){
            return self::$sm_logger->$name(...$arguments);
        }
    }
}