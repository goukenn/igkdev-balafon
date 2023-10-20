<?php

// @author: C.A.D. BONDJE DOUE
// @filename: SyncProjectCommand.php
// @date: 20220502 12:51:36
// @desc: sync project to an througth ftp 
namespace IGK\System\Console\Commands\Sync;

use IGK\Helper\FtpHelper;
use IGK\Helper\PhpUnitHelper;
use IGK\System\Console\App;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\System\IO\File\PHPScriptBuilderUtility;
use IGK\System\IO\StringBuilder;
use IGK\System\Shell\OsShell;

/**
 * clear cache in ftp sync server */
class SyncUpdateCoreLibCommand extends SyncAppExecCommandBase
{
    var $command = "--sync:update-corelib";
    var $category = "sync";
    var $desc = "sync balafon corelib";
    var $options = [
        "--force" => "flag: force, do not check library before sync",
        "--core-test-suite"=>"core test-suite to check. default is set in balafon.config.xml",
        "--install-site"=>"flag: install site if complete"
    ];
    public function exec($command)
    {
        if (($c = $this->initSyncSetting($command, $setting)) && !$setting) {
            return $c;
        }
        if (!isset($setting["site_uri"])) {
            Logger::danger("site uri not provided");
            return false;
        }
        $force = property_exists($command->options, "--force");

        // + | check lib before update 
        if (!$force) {
            Logger::info("checking library before sync...");
            if ($phpunit = OsShell::Where('phpunit')) {
                $core_suite = igk_getv($command, '--core-test-suite', 'core');
                $r = PhpUnitHelper::TestCoreProject($phpunit, $core_suite);
                if ($r) {
                    return $r;
                }
            }
        }

        if (!is_object($h = $this->connect($setting["server"], $setting["user"], $setting["password"]))) {
            return $h;
        }
        $pdir = $setting["public_dir"];
        $uri = $setting["site_uri"];
        $install_dir = $setting["lib_dir"] ?? "../application/Lib/igk";



        Logger::info(sprintf("update core lib to [ %s ]", $setting["server"]));

        // copy libzip to public folder 
        // copy exec script to public folder
        // execute the install libscript
        // delete install libscript
        // delete zipfile 
        $temp_file = igk_io_sys_tempnam("blfcore");
        $script_install = igk_io_sys_tempnam("blfcore");

        unlink($temp_file);
        Logger::info("temp file : " . $temp_file);
        Logger::info("temp script :" . $script_install);
        $sb = new StringBuilder();
        $token = date("Ymd") . rand(2, 85) . igk_create_guid();
        $sb->appendLine(implode("\n", [
            "\$token = '" . $token . "';",
        ]));

        $src = PHPScriptBuilderUtility::MergeSource(
            IGK_LIB_DIR . "/Inc/core/class.InstallerResponse.pinc",
            IGK_LIB_DIR . "/Inc/core/install.script.pinc",
            IGK_LIB_DIR . "/Inc/core/installer.helper.pinc"
        );      
        $sb->appendLine("?>" . $src);  
        $sb->appendLine("echo 'finish install';");
        $sb->appendLine("@unlink(__FILE__);");

        $builder = new PHPScriptBuilder();
        $builder->type("function")
            ->defs($sb);
        igk_io_w2file($script_install, $builder->render());
        igk_sys_zip_core($temp_file, false);
        $dir = ftp_pwd($h);
        if (!@ftp_chdir($h, $pdir)) {
            FtpHelper::CreateDir($h, $pdir);
        }
        ftp_chdir($h, $dir);

        ftp_put($h, $lib =  $pdir . "/corelib.zip", $temp_file, FTP_BINARY);
        ftp_put($h, $install = $pdir . "/install.php", $script_install, FTP_BINARY);

        unlink($temp_file);
        unlink($script_install);
        $response = null;
        $response = igk_curl_post_uri(
            $uri . "/install.php",
            [
                "install_dir" => $install_dir,
                "token" => $token,
                "app_dir" => $setting["application_dir"],
                "home_dir" => $setting["home_dir"],
                "ftp_home_dir" => $dir
            ],
            null,
            [
                "install-token" => $token
            ]
        );


        FtpHelper::RmFile($h, $lib);
        FtpHelper::RmFile($h, $install);
        ftp_close($h);
        if (($status = igk_curl_status()) == 200) {
            Logger::info("curl response \n" . App::Gets(App::BLUE, $response));
            $rep = json_decode($response);
            if ($rep && !$rep->error) {
                Logger::success("update core lib success");
                if ($setting['site_uri']) {
                    Logger::print('you can navigate to: ' . $setting['site_uri']);
                }

                if (property_exists($command->options, "--install-site")){
                    $exec_command = new SyncInstallSiteCommand;
                    $new_command = self::CreateOptionsCommandFrom($command,[ 
                        "--no-subdomai"=>igk_getv($command->options, "--no-subdomai"),
                        "--no-webconfig"=>igk_getv($command->options, "--no-webconfig") 
                        // "--admin-login" =>"set configuration login",
                        // "--admin-pwd"   =>"set configuration login",
                    ]);

                    return $exec_command->exec($new_command);
                
                
                }
            }
        } else {
            Logger::danger("install script failed");
            Logger::info("status : " . $status);
            Logger::print(json_encode($response, JSON_UNESCAPED_SLASHES));
        }
        error_clear_last();
    }
}
