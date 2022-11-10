<?php

// @author: C.A.D. BONDJE DOUE
// @filename: SyncProjectCommand.php
// @date: 20220502 12:51:36
// @desc: sync project to an througth ftp 
namespace IGK\System\Console\Commands;

use IGK\Helper\FtpHelper;
use IGK\System\Console\Logger;

/**
 * clear cache in ftp sync server */
class SyncUnlockSiteCommand extends SyncAppExecCommandBase
{
    var $command = "--sync:unlock-site";
    var $category = "sync";
    var $desc = "sync unlock site";

    public function exec($command)
    {
        if ( ($c = $this->initSyncSetting($command, $setting)) && !$setting){
            return $c;
        } 
        if (!is_object($h = $this->connect($setting["server"],$setting["user"], $setting["password"]))){
            return $h;
        }
        $pdir = $setting["public_dir"];
        if (FtpHelper::FileExists($h, $pdir."/.maintenance.lock" )){        
            Logger::info("unlock site");
            FtpHelper::RmFile($h, $pdir."/index.php"); 
            FtpHelper::RmFile($h, $pdir."/.htaccess"); 
            FtpHelper::RmFile($h, $pdir."/.maintenance.lock"); 

            FtpHelper::RenameFile($h, $pdir."/.lock.index.php", $pdir."/index.php"); 
            FtpHelper::RenameFile($h, $pdir."/.lock.htaccess", $pdir."/.htaccess"); 
        } else {
            Logger::info("site not locked");
        }
        ftp_close($h);
        error_clear_last();
    }
}