<?php

// @author: C.A.D. BONDJE DOUE
// @filename: OsShell.php
// @date: 20220426 10:34:04
// @desc: 


namespace IGK\System\Installers;



class OsShell {    
    private static $sm_commands = [
        "Unix"=>OsUnixCommand::class,
        "Window"=>OsWinCommand::class
    ];
    public static function Register(string $type, string $class ){
        self::$sm_commands[$type] = $class;
    }
    public static function __callStatic($n, $args){        
        if (igk_environment()->isUnix()){
            $cl = self::$sm_commands["Unix"];            
        }else{
            $cl = self::$sm_commands["Window"];            
        } 
        return call_user_func_array([$cl, $n], $args);
    }
     
}
