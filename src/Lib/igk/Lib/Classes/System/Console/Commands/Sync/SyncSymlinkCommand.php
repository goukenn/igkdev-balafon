<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SyncSymlinkCommand.php
// @date: 20220729 13:58:01
// @desc: 


namespace IGK\System\Console\Commands\Sync;

use IGK\Controllers\ApplicationModuleController;
use IGK\Helper\FtpHelper;
use IGK\Helper\IO;
use IGK\System\Console\App;
use IGK\System\Console\Logger;
use IGK\System\Controllers\ApplicationModules;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\System\IO\File\PHPScriptBuilderUtility;
use IGK\System\IO\StringBuilder;
use IGK\System\Regex\Replacement;

/**
 * sync ftp project
 * @package IGK\System\Console\Commands
 */
class SyncSymlinkCommand extends SyncAppExecCommandBase
{
    var $command = "--sync:symlink";
    var $desc = "create site symbolic link";
    var $category = "sync";
    var $help = "";
    var $options = [

    ];
    public function showUsage(){
        Logger::print(sprintf("%s path site_target [options]", $this->command));
    }

    public function exec($command, ?string $dir = null, ?string $target=null) {
        if (($c = $this->initSyncSetting($command, $setting)) && !$setting) {
            return $c;
        }
        if (!is_object($h = $this->connect($setting["server"], $setting["user"], $setting["password"]))) {
            return -2;
        }
        is_null($dir) && igk_die('mising dir');
        is_null($target) && igk_die('mising target');
        $file = tempnam(sys_get_temp_dir(), "blf");
        unlink($file);

        $content = PHPScriptBuilderUtility::MergeSource(        
                IGK_LIB_DIR."/Inc/core/installer-helper.pinc",
                IGK_LIB_DIR."/Inc/core/symlink.pinc",          
        ); 
        $pdir = $setting["public_dir"];
        $uri = $setting["site_uri"];
        $install = $pdir."/_symlink.php";
        igk_io_w2file($file, $content);
        ftp_put($h, $install, $file);

        // $dir = igk_io_expand_path($dir);
        $rp = new Replacement;
        $rp->add("/%module%/", $setting["application_dir"]."/Packages/Modules");
        $rp->add("/%lib%/", $setting["lib_dir"]);
        $dir = $rp->replace($dir);
        
        $token = date("Ymd").rand(2, 85).igk_create_guid();
        $response = igk_curl_post_uri(
            $uri . "/_symlink.php",
            [ 
                "token" => $token,
                "dir" => $dir,
                "target"=>$target ,
                "force"=>property_exists($command->options, "--force"),
                "home_dir"=>igk_getv($setting, "home_dir", "")
            ],
            null,
            [
                "install-token" => $token
            ]
        );
        FtpHelper::RmFile($h, $install);
        ftp_close($h);
        Logger::print($response);
     }
 
}