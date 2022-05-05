<?php

// @author: C.A.D. BONDJE DOUE
// @filename: Untitled-1
// @date: 20220505 15:11:46
// @desc: 
namespace IGK\System\Console\Commands;

use IGK\Helper\FtpHelper;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;

abstract class SyncAppExecCommandBase extends AppExecCommand{

    protected function initSyncSetting($command, & $setting){
        $setting = null;
        $sync = $command->app->getConfigs()->get("ftp-sync");
        if (!$sync) {
            Logger::danger("No ftp-sync available");
            return -100;
        }
        $setting = [
            "server" => $sync->server,
            "password" => $sync->password,
            "user" => $sync->user,
            "release" => igk_getv($sync, "release", $sync->application."/Data/releases"),
            "path" => igk_getv($sync, "project", $sync->application."/Projects"),
            "application_dir"=> $sync->application
        ];

        return $sync;

    }
    protected function connect($server, $user, $pwd){
        $h = null;
        // connect to ftp server
        if (!$h = ftp_connect($server)) {
            Logger::danger("fail to connect to " . $server);
            return -4;
        }
        // authenticate 
        if (!ftp_login($h, $user, $pwd)) {
            Logger::danger("fail to login to " . $server);
            return -5;
        }
        return $h;
    }

    protected function removeCache($ftp, $app_dir){        
        FtpHelper::RmDir($ftp, $app_dir."/.Caches"); 
    }
}
