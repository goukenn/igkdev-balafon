<?php

// @author: C.A.D. BONDJE DOUE
// @filename: SyncProjectCommand.php
// @date: 20220502 12:51:36
// @desc: sync project to an througth ftp 
namespace IGK\System\Console\Commands;

use IGK\Helper\FtpHelper;
use IGK\System\Console\Logger;
use IGK\System\Installers\InstallerUtils;

/**
 * clear cache in ftp sync server */
class SyncInstallSite extends SyncAppExecCommandBase
{
    var $command = "--sync:install-site";
    var $category = "sync";
    var $desc = "sync init public folder";

    var $options = [
        "--no-subdomain"=>"disable subdomain support",
        "--no-webconfig"=>"disable web configuration",
    ];

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

        $app_dir = $setting[self::APP_DIR];
        $sess_dir = $setting[self::SESSION_DIR] ?? $app_dir."/../sesstemp";

        FtpHelper::CreateDir($h, $app_dir);
        FtpHelper::CreateDir($h, $sess_dir);

        $is_base = $app_dir == $setting["lib_dir"];

        FtpHelper::CreateDir($h, $app_dir."/".IGK_LIB_FOLDER);
        FtpHelper::CreateDir($h, $app_dir."/" . IGK_PACKAGES_FOLDER);
        FtpHelper::CreateDir($h, $app_dir."/" . IGK_PACKAGES_FOLDER."/vendor");
        FtpHelper::CreateDir($h, $app_dir."/" . IGK_PACKAGES_FOLDER."/Modules");
        FtpHelper::CreateDir($h, $app_dir."/" . IGK_PACKAGES_FOLDER."/Lib");
        FtpHelper::CreateDir($h, $app_dir."/".IGK_PROJECTS_FOLDER);
        FtpHelper::CreateDir($h, $app_dir."/".IGK_DATA_FOLDER);
        FtpHelper::CreateDir($h, $app_dir."/".IGK_INC_FOLDER);
        FtpHelper::CreateDir($h, $app_dir."/".IGK_CGI_BIN_FOLDER);
        FtpHelper::CreateDir($h, $app_dir."/".IGK_SCRIPT_FOLDER);


        $pdir = $setting["public_dir"];
        $uri = $setting["site_uri"];
        $no_subdomain = property_exists($command->options, '--no-subdomain');
        $no_webconfig = property_exists($command->options, '--no-webconfig');
        SyncInitPublicCommand::InstallFolder($h, $pdir, $uri, $no_subdomain, $no_webconfig);

        if (!$is_base){
             
            $install = $pdir."/install-site.php";
            $local_file = tempnam(sys_get_temp_dir(), "blf-install-site");
            file_put_contents($local_file, file_get_contents(IGK_LIB_DIR."/Inc/core/install-site.pinc"));
            ftp_put($h, $install, $local_file);
            unlink($local_file);

            $token = date("Ymd").rand(2, 85).igk_create_guid();
            
            $response = igk_curl_post_uri($uri."/install-site.php", 
                [
                    "corelib"=>$setting["lib_dir"],
                    "token"=>$token,
                    "app_dir"=>$setting["application_dir"],
                    "home_dir"=>$setting["home_dir"],
                    "root_dir"=>$setting["public_dir"],
                    self::SITE_DIR=>$setting["site_dir"],
                    "no_subdomain"=>$no_subdomain,
                    "no_webconfig"=>$no_webconfig,
                ], null, [
                "install-token"=>$token
            ]);

            if ($response){
                Logger::info($response);
            }
        }

         
        ftp_close($h);
        Logger::info("done"); 
        error_clear_last();
    }
}