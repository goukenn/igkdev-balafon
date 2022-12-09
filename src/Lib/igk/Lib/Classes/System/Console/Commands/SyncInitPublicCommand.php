<?php

// @author: C.A.D. BONDJE DOUE
// @filename: SyncProjectCommand.php
// @date: 20220502 12:51:36
// @desc: sync project to an througth ftp 
namespace IGK\System\Console\Commands;

use IGK\Helper\FtpHelper;
use IGK\System\Console\Logger;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Installers\InstallerUtils;
use IGKException;

/**
 * clear cache in ftp sync server */
class SyncInitPublicCommand extends SyncAppExecCommandBase
{
    var $command = "--sync:init-public-dir";
    var $category = "sync";
    var $desc = "sync init public folder";

    public function exec($command)
    {
        if ( ($c = $this->initSyncSetting($command, $setting)) && !$setting){
            return $c;
        } 
        if (!is_object($h = $this->connect($setting["server"],$setting["user"], $setting["password"]))){
            return $h;
        }
        if (!isset($setting["site_uri"])){
            Logger::danger("site uri not provided");
            return false;
        }

        $pdir = $setting["public_dir"];
        $uri = $setting["site_uri"];
        self::InstallFolder($h, $pdir, $uri);
       
        ftp_close($h);
        Logger::info("done"); 
        error_clear_last();
    }
    /**
     * 
     * @param mixed $h resource
     * @param mixed $pdir public dir
     * @param mixed $uri  uri access
     * @return void 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */
    public static function InstallFolder($h, string $pdir, string $uri, $no_subdomain=false, $no_webconfig = false){
        $index_temp = igk_io_sys_tempnam("blfcore");
        $access_temp = igk_io_sys_tempnam("blfcore");
        $is_primary = false;
        igk_io_w2file($index_temp,
        InstallerUtils::GetEntryPointSource([
            "is_primary" => $is_primary,
            "app_dir" => $is_primary ? '$appdir' : '$appdir."/application"',
            "project_dir" => 'IGK_APP_DIR."/Projects"',
            "no_subdomain"=>$no_subdomain,
            "no_webconfig"=>$no_webconfig,
        ]));
        $token = igk_create_guid();

        igk_io_w2file($access_temp,           
            igk_getbase_access($pdir)
        );

        FtpHelper::CreateDir($h, $pdir."/assets");

        ftp_put($h,$pdir."/index.php", $index_temp );
        ftp_put($h,$pdir."/.htaccess", $access_temp ); 
        igk_curl_post_uri($uri, null, null, ["sync-command"=>true, "token"=>$token]);
        unlink($index_temp);
        unlink($access_temp); 
    }
}