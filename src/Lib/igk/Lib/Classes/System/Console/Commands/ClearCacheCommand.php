<?php

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;

class ClearCacheCommand extends AppExecCommand{
    var $command = "--clearcache";
    
    var $desc = "clear cache command";
    
    /**
     * exec the command
     */
    public function exec($command)
    { 
        //defined("NO")
        Logger::print("Cache directory : ".igk_io_cachedir()); 
        // IO::RmDir($cdir);
        IGKControllerManagerObject::ClearCache();
        Logger::success("done");
    }
    
}
 