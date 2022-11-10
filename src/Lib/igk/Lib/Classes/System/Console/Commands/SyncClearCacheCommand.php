<?php

// @author: C.A.D. BONDJE DOUE
// @filename: SyncProjectCommand.php
// @date: 20220502 12:51:36
// @desc: sync project to an througth ftp 
namespace IGK\System\Console\Commands; 
use IGK\System\Console\Logger;

/**
 * clear cache in ftp sync server */
class SyncClearCacheCommand extends SyncAppExecCommandBase
{
    var $command = "--sync:clearcache";
    var $category = "sync";
    var $desc = "sync clear cache";

    public function exec($command)
    {
        if ( ($c = $this->initSyncSetting($command, $setting)) && !$setting){
            return $c;
        }
        if (!is_object($h = $this->connect($setting["server"],$setting["user"], $setting["password"]))){
            return $h;
        }
        Logger::info("remove cache");
        $this->removeCache($h, $setting["application_dir"]);
        ftp_close($h);
        error_clear_last();
    }
}