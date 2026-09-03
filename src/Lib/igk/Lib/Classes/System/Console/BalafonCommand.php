<?php
// @author: C.A.D. BONDJE DOUE
// @filename: BalafonCommand.php
// @date: 20220605 01:21:10
// @desc: command
namespace IGK\System\Console;
use IGK\System\Shell\OsShell;

/**
* auto generate doc.
* @package IGK\System\Console
*/
class BalafonCommand{
    /**
     * primary 
     * @return void 
     */
    protected function __construct()
    {
    }
    /**
     * execute balafon command
     * @param string $commandArgs 
     * @return never 
     */
    public static function Exec(string $commandArgs){
        if (! (OsShell::Where("balafon"))){
            $path = getenv('PATH');
            putenv("PATH=". $path.PATH_SEPARATOR.dirname(IGK_LIB_BIN));            
        }
        $c = new static();
        return $c->run($commandArgs);
    }
    /**
     * return command args
     * @param mixed $commandArgs 
     * @return string 
     */
    protected function run($commandArgs){
        $bin = igk_configs()->php_bin;
        $s = '';
        if ($bin && file_exists($bin)){
            $s = $bin .' '.IGK_LIB_BIN.' '.$commandArgs;
        }else 
            $s = 'balafon '.$commandArgs;  
        igk_ilog('try run > '.$s);
        return shell_exec("{$s}");
    }
}