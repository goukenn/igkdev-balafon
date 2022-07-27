<?php

// @author: C.A.D. BONDJE DOUE
// @filename: Untitled-1
// @date: 20220505 15:11:46
// @desc: sync ftp commmand base
// <ftp-sync name="name">
// 		<server>server</server>
// 		<user>user</user>
// 		<password>pwd</password>
// 		<core></core>
// 		<site_uri></site_uri>
// 		<application></application>
// 		<public_dir></public_dir>
// 		<release></release>
// 		<project></project>
// 		<lib_dir></lib_dir>
// 	</ftp-sync>
// ...


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
        $name = igk_getv($command->options, "--name");        
        if (is_array($sync)){
            foreach($sync as $s){ 
                if (igk_getv($s, "name") == $name){
                    $sync = $s;
                    break;
                }
            }
        }
        

        $app = $sync->application;
        $setting = [
            "server" => $sync->server,
            "password" => $sync->password,
            "user" => $sync->user,
            "release" => igk_getv($sync, "release", $sync->application."/Data/releases"),
            "path" => igk_getv($sync, "project", $sync->application."/Projects"),
            "application_dir"=> $sync->application,
            "public_dir"=>igk_getv($sync, "public_dir", $app),
            "site_uri" => igk_getv($sync , "site_uri"),
            "lib_dir"=>igk_getv($sync , "lib_dir"),
        ];
        return $sync;

    }
    protected function connect($server, $user, $pwd){
        $h = null;
        // connect to ftp server
        if (!$h = @ftp_connect($server)) {
            Logger::danger("fail to connect to " . $server);
            error_clear_last();
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
