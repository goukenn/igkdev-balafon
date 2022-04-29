<?php

use IGK\Controllers\SessionController;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;

class ClearSessionCommand extends AppExecCommand{
    var $command = "--clearsession";    
    var $desc = "clear session command";
    
    /**
     * exec the command
     */
    public function exec($command)
    { 
        //defined("NO")
        /**
         * @var SessionController $sess
         */
        if ($sess = igk_getctrl(IGK_SESSION_CTRL, false)){
            Logger::info("Clearing session");
            $sess->clearAllSession();
        }      
 
        Logger::success("done");
    }
    
}
 