<?php

// @author: C.A.D. BONDJE DOUE
// @filename: Untitled-1
// @date: 20220505 15:11:46
// @desc: sync ftp commmand base
// <ftp-sync name="name">
// 		<server>server</server>
// 		<user>user</user>
// 		<password>pwd</password>
// 		<core>core framework directory</core>
// 		<site_uri></site_uri>
// 		<application>appication directory</application>
// 		<project>project directory</project>
// 		<public_dir>public access directory</public_dir>
// 		<release>where to backup project release</release>
// 		<lib_dir>site lib directory application/Lib</lib_dir>
//      <module_dir>dir where store modules</module_dir>
//      <node_dir>dir where store node modules </node_dir>
//      <module_dir>dir where store modules</module_dir>
//      <composer_dir>dir where store composer</composer_dir>
//      <session_dir>dir where store sessions</session_dir>  
//      <home_dir>ftp home directory to use in case of missing $_SERVER['HOME']</home_dir>  
// 	</ftp-sync>
// ...


namespace IGK\System\Console\Commands;

use IGK\Helper\FtpHelper;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;

abstract class SyncAppExecCommandBase extends AppExecCommand{
    // + | entry config tagname
    const SELF_KEY_CONFIG = 'ftp-sync';
    // + | configuration keys
    const SESSION_DIR = "session_dir";
    const APP_DIR = "application_dir";
    const PROJECT_DIR = "project_dir";
    const RELEASE_DIR = "release_dir";
    const SITE_DIR = "site_dir";
    const HOME_DIR = "home_dir";

    protected function initSyncSetting($command, & $setting){
        $setting = null;
        $sync = $command->app->getConfigs()->get(self::SELF_KEY_CONFIG); 

        if (!$sync) {
            Logger::danger(sprintf("No %s available", self::SELF_KEY_CONFIG));
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
        if (is_array($sync) && !empty($name)){
            Logger::danger("No name found");
            return -200;
        }
        if (is_array($sync)){
            Logger::danger(sprintf("no default %s configuration found.", self::SELF_KEY_CONFIG));
            return -201;
        }

        $app = $sync->application;
        $pwd = "";
        if (is_object($sync->password)){
            if (igk_getv($sync->password, 'encoding') == 'base64' ){
                $pwd = base64_decode($sync->password->value);
            } else {
                $pwd = $sync->password->value;
            }
        }else{
            $pwd = $sync->password;
        } 
        $setting = [
            "server" => $sync->server,
            "password" => $pwd,
            "user" => $sync->user,
            self::APP_DIR=> $app,
            self::RELEASE_DIR => igk_getv($sync, self::RELEASE_DIR, $app."/Data/releases"),
            self::PROJECT_DIR => igk_getv($sync, self::PROJECT_DIR, $app."/Projects"),
            "public_dir"=>igk_getv($sync, "public_dir", $app),
            "site_uri" => igk_getv($sync , "site_uri"),
            "lib_dir"=>igk_getv($sync , "lib_dir"),
            "module_dir"=>igk_getv($sync , "module_dir"),
            "node_dir"=>igk_getv($sync , "node_dir"),
            "composer_dir"=>igk_getv($sync , "composer_dir"),
            self::SESSION_DIR =>igk_getv($sync , "session_dir"),
            self::SITE_DIR =>igk_getv($sync ,self::SITE_DIR),
            self::HOME_DIR=>igk_getv($sync , self::HOME_DIR),
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
    /**
     * remove all item in directory
     * @param mixed $frp 
     * @param mixed $dir 
     * @return void 
     */
    protected function emptyDir($ftp, string $dir){
        FtpHelper::RmDir($ftp, $dir); 
        FtpHelper::CreateDir($ftp, $dir);
    }
}
