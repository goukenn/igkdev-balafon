<?php

// @author: C.A.D. BONDJE DOUE
// @filename: OsShell.php
// @date: 20220426 10:34:04
// @desc: 


namespace IGK\System\Shell;


/**
 * helper to get sheel
 * @package IGK\System\Shell
 */
class OsShell {    
    private static $sm_commands = [
        "Unix"=>OsUnixCommand::class,
        "Window"=>OsWinCommand::class
    ];
    public static function ExecInWorkingDir(string $command, string $workingdir, ?string $success=null){
        $bck = getcwd();
        chdir($workingdir);
        if ($success){
            $success = " && echo '{$success}'";   
        }
        $o = `$command{$success}`;        
        chdir($bck);
        return $o;
    }
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
    public static function Exec($command){ 
        return exec($command);
    }
     
}
