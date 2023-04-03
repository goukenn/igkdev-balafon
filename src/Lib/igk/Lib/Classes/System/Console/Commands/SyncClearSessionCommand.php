<?php

// @author: C.A.D. BONDJE DOUE
// @filename: SyncProjectCommand.php
// @date: 20220502 12:51:36
// @desc: sync project to an througth ftp 
namespace IGK\System\Console\Commands;

use IGK\Helper\FtpHelper;
use IGK\System\Console\Logger;
use IGK\System\IO\Path;

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
        $sess_dir = Path::FlattenPath($setting[self::SESSION_DIR] ?? $setting[self::APP_DIR]."/../sesstemp"); //  ?? igk_die("no session dir provided");
        Logger::info("remove all lived session : ". $sess_dir);
        igk_set_timeout(0);

        $script_install = igk_io_sys_tempnam("blf_module_script");
        $uri = $setting["site_uri"];
        $pdir = $setting["public_dir"];
        $sb = self::GetScriptInstall([
            "installer-helper.pinc",
            "sync.command.pinc"
        ], $token, "remove session");
        igk_io_w2file($script_install, $sb);
        ftp_put($h, $install = $pdir . "/rm_sessions.php", $script_install, FTP_BINARY);
        if ($output = igk_curl_post_uri(
            $uri."/rm_sessions.php",
            [
                "dir"=>$sess_dir,
                "cmd"=>"clearsession",
                "home_dir"=>$setting["home_dir"],
            ],
            null,
            [
                "install-token" => $token
            ]
        )){
            Logger::print("response");
            Logger::print($output);
        }
        unlink($script_install);
        FtpHelper::RmFile($h, $install);
        ftp_close($h);
        error_clear_last();
    }
}