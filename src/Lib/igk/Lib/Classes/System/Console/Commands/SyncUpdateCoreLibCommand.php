<?php

// @author: C.A.D. BONDJE DOUE
// @filename: SyncProjectCommand.php
// @date: 20220502 12:51:36
// @desc: sync project to an througth ftp 
namespace IGK\System\Console\Commands;

use IGK\Helper\FtpHelper;
use IGK\System\Console\App;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\System\IO\StringBuilder;

/**
 * clear cache in ftp sync server */
class SyncUpdateCoreLibCommand extends SyncAppExecCommandBase
{
    var $command = "--sync:update-corelib";
    var $category = "sync";
    var $desc = "sync balafon corelib";

    public function exec($command)
    {
        if ( ($c = $this->initSyncSetting($command, $setting)) && !$setting){
            return $c;
        } 
        if (!isset($setting["site_uri"])){
            Logger::danger("site uri not provided");
            return false;
        }
        if (!is_object($h = $this->connect($setting["server"],$setting["user"], $setting["password"]))){
            return $h;
        }
        $pdir = $setting["public_dir"];
        $uri = $setting["site_uri"];
        $install_dir = $setting["lib_dir"] ?? "../application/Lib/igk";
        Logger::info("update core lib");

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
        $token = date("Ymd").rand(2, 85).igk_create_guid();
        $sb->appendLine(implode("\n", [
            "\$token = '".$token."';",            
        ]));
        // $sb->appendLine("print_r(\$_REQUEST); \n exit;");
        $sb->appendLine("?>".file_get_contents(IGK_LIB_DIR."/Inc/core/install.script.pinc"));
        $sb->appendLine("echo 'finish install'; @unlink(__FILE__);");

        $builder = new PHPScriptBuilder();
        $builder->type("function")
        ->defs($sb);
        igk_io_w2file($script_install, $builder->render());
        igk_sys_zip_core($temp_file, false);     

        ftp_put($h,$lib =  $pdir."/corelib.zip", $temp_file, FTP_BINARY);
        ftp_put($h,$install= $pdir."/install.php", $script_install, FTP_BINARY);        

        unlink($temp_file);
        unlink($script_install);
        $response = null;
        $response = igk_curl_post_uri($uri."/install.php", 
            [
                "install_dir"=>$install_dir,
                "token"=>$token,
                "app_dir"=>$setting["application_dir"]
            ], null, [
            "install-token"=>$token
        ]);

        
        FtpHelper::RmFile($h,$lib);
        FtpHelper::RmFile($h,$install);
        ftp_close($h);
        if (($status = igk_curl_status())== 200){
            Logger::info("curl response \n". App::gets(App::BLUE, $response));
            $rep = json_decode($response);
            if (!$rep->error){
                Logger::success("update core lib success");
            }
        }else {
            Logger::danger("install script failed");
            Logger::info( "status : ".$status); 
        }        
        error_clear_last();
    }
}