<?php

// @author: C.A.D. BONDJE DOUE
// @filename: SyncProjectCommand.php
// @date: 20220502 12:51:36
// @desc: sync project to an througth ftp 
namespace IGK\System\Console\Commands; 
use IGK\System\Console\Logger;

/**
 * clear sites session 
 *  */
class SyncClearSessionCommand extends SyncAppExecCommandBase
{
    var $command = "--sync:clearsession";
    var $category = "sync";
    var $desc = "sync clear session";

    public function exec($command)
    {
        if ( ($c = $this->initSyncSetting($command, $setting)) && !$setting){
            return $c;
        }
        if (!is_object($h = $this->connect($setting["server"],$setting["user"], $setting["password"]))){
            return $h;
        }
        $sess_dir = $setting[self::SESSION_DIR] ?? $setting[self::APP_DIR]."/../sesstemp"; //  ?? igk_die("no session dir provided");
        Logger::info("remove all lived session");
        igk_set_timeout(0);
        $this->emptyDir($h, $sess_dir); 
        ftp_close($h);
        error_clear_last();
    }
}