<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ClearCacheCommand.php
// @date: 20220803 13:48:57
// @desc: 

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
 