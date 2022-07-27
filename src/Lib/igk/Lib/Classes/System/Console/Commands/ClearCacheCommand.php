<?php
namespace IGK\System\Console\Commands;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;
use IGKControllerManagerObject;

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
        IGKControllerManagerObject::ClearCache();
        Logger::success("done");
    }
    
}
 