<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ClearCacheCommand.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\System\Console\Commands;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;


class ClearCacheCommand extends AppExecCommand{
    var $command = "--clearcache";
    
    var $desc = "clear cache command";

    var $category = "administration";
    
    /**
     * exec the command
     */
    public function exec($command)
    { 
        //defined("NO")
        Logger::print("Cache directory : ".igk_io_cachedir());  
        \IGK\Helper\SysUtils::ClearCache();
        Logger::success("done");
    }
    
}
 