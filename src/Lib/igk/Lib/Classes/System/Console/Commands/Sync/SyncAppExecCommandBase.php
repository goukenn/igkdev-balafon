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


namespace IGK\System\Console\Commands\Sync;

use IGK\Helper\FtpHelper;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilderUtility;
use IGK\System\IO\StringBuilder;

abstract class SyncAppExecCommandBase extends AppExecCommand{

    var $category = "sync";
    
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
        $defsync = null;       
        if (is_array($sync)){
            foreach($sync as $s){ 
                if (igk_getv($s, "name") == $name){
                    $sync = $s;
                    break;
                }
                if (igk_bool_val(igk_getv($s, "default"))){
                    $defsync = $s;                    
                }
            }
        } 
        if ($defsync && is_array($sync)) {
            $sync = $defsync;
        }
        if (is_array($sync) && !empty($name)){
            Logger::danger("No name found");
            return -200;
        }
        if (!empty($name) && ($sync->name != $name)){
            Logger::danger(sprintf("missing name :  [%s].", $name));
            return -203;
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

    /**
     * get install script 
     */
    protected static function GetScriptInstall($script, & $token, $name=null){
        $src = null;
        if (is_array($script)){
            $tab = array_filter(array_map(function($a){
                if (is_file($f = IGK_LIB_DIR."/Inc/core/".$a)){
                    return $f;
                }
                return null;
            }, $script));
            $src = PHPScriptBuilderUtility::MergeSource(        
                ...$tab        
            );
         
        }else{
            $file  = IGK_LIB_DIR . "/Inc/core/".$script;
            if (!file_exists($file)){
                return false;
            }
            $src = file_get_contents($file);
        }
        if (empty($src)){
            return false;
        }
        $sb = new StringBuilder();
        $token = date("Ymd") . rand(2, 85) . igk_create_guid();
        $sb->appendLine(implode("\n", array_filter([
            "\$token = '" . $token . "';",
           $name ?  "\$archive= '" . $name . "';" : null,
        ])));

        $src = str_replace("// %token%", $sb."", $src);
        $sb->clear();
        $sb->appendLine($src);
        $sb->appendLine("@unlink(__FILE__);");        
        return $sb."";
    }

    protected function syncScriptCommand($command, $script, $args){
        if (($c = $this->initSyncSetting($command, $setting)) && !$setting) {
            return $c;
        }
        if (!is_object($h = $this->connect($setting["server"], $setting["user"], $setting["password"]))) {
            return -2;
        } 
        $file = tempnam(sys_get_temp_dir(), "blf");
        unlink($file);

        $content = PHPScriptBuilderUtility::MergeSource( ...$this->getMergedScripts());  


        $pdir = $setting["public_dir"];
        $uri = $setting["site_uri"];
        $install = $pdir."/".$script;
        igk_io_w2file($file, $content);
        ftp_put($h, $install, $file); 
        $curl_options = null;
        if (property_exists($command->options, '--no-timeout')){
            set_time_limit(0);
            $curl_options[CURLOPT_TIMEOUT] = 0;
        }
        
        $token = date("Ymd").rand(2, 85).igk_create_guid();
        $response = igk_curl_post_uri(
            $uri . "/".$script,
            array_merge([ 
                "token" => $token,
                // "dir" => $dir, 
                "force"=>property_exists($command->options, "--force"),
                "home_dir"=>igk_getv($setting, "home_dir", ""),
                "public_dir"=>$pdir,
                "package_dir"=>$setting["application_dir"]."/Packages",
            ], $args),
            $curl_options,
            [
                "install-token" => $token
            ]
        );
        FtpHelper::RmFile($h, $install);
        ftp_close($h);
        Logger::info("response");
        if ($response){
            Logger::print($response);
        }
    }
    public function getHelpOptions(){
        return ['--no-timeout'=>'flag: disable timeout'];
    }
}
