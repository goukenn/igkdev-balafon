<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SyncProjectCommand.php
// @date: 20220502 12:51:36
// @desc: sync project to an througth ftp 
namespace IGK\System\Console\Commands\Sync;
use IGK\Helper\FtpHelper;
use IGK\System\Console\Logger;
use IGK\System\IO\Path;

/**
 * clear sites session 
 *  */
class SyncClearSessionCommand extends SyncAppExecCommandBase
{
    /**
    * Property: command.
    * @var mixed
    */
    var $command = "--sync:clearsession";
    /**
    * Property: category.
    * @var mixed
    */
    var $category = "sync";
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = "sync:ftp clear session";
    /**
    * Exec.
    * @param mixed $command
    */
    public function exec($command)
    { 
        if ( ($c = $this->initSyncSetting($command, $setting)) && !$setting){
            return $c;
        }
        if (!is_object($h = $this->connect($setting["server"],$setting["user"], $setting["password"]))){
            return $h;
        }
        $sess_dir = Path::FlattenPath($setting[self::SESSION_DIR] ?? $setting[self::APP_DIR]."/../sesstemp"); 
        Logger::info("remove all lived session : ". $sess_dir);
        igk_set_timeout(0);
        $script_install = igk_io_sys_tempnam("blf_module_script");
        $uri = $setting["site_uri"];
        $pdir = $setting["public_dir"];
        $sb = self::GetScriptInstall([
            'installer-core-function.pinc',
            "installer-helper.pinc",
            "sync.command.pinc"
        ], $token, "remove session");
        igk_io_w2file($script_install, $sb);
        $r = shell_exec("php -l $script_install");
        if ($r && strstr($r , 'Parse error')){
            Logger::danger("parsing error - ");
            return -103;
        }
        $v_puts = @ftp_put($h, $install = $pdir . "/rm_sessions.php", $script_install, FTP_BINARY);
        if (!$v_puts){
            Logger::danger("failed to upload script");
            return -102;
        }
        if ($output = igk_curl_post_uri(
            $uri."/rm_sessions.php",
            [
                "dir"=>$sess_dir,
                "cmd"=>"clearsession",
                "home_dir"=>$setting[self::HOME_DIR],
            ],
            null,
            [
                "install-token" => $token
            ]
        )){
            igk_ob_clean();
            Logger::print("response: ");
            Logger::print($output);
        } else {
            $st = igk_curl_status();
            Logger::danger("something bad happend. failed to exec ". $st); 
        }
        unlink($script_install);
        Logger::info('remove script');
        FtpHelper::RmFile($h, $install);
        ftp_close($h);
        error_clear_last();
        Logger::print("done");
    }
}