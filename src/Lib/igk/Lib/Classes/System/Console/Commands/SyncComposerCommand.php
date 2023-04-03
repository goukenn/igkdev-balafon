<?php

namespace IGK\System\Console\Commands;

use IGK\Helper\FtpHelper;
use IGK\System\Console\Commands\SyncAppExecCommandBase;
use IGK\System\Console\Logger;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\IO\File\PHPScriptBuilderUtility;
use IGK\System\Regex\Replacement;
use IGKException;
use ReflectionException;

class SyncComposerCommand extends SyncAppExecCommandBase{
    var $command = "--sync:composer";
    
    /**
     * get merged scripts
     * @return string[] 
     */
    protected function getMergedScripts(){
        return [
            IGK_LIB_DIR."/Inc/core/installer-helper.pinc",
            IGK_LIB_DIR."/Inc/core/composer.pinc",   
        ];
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

        // $dir = igk_io_expand_path($dir);
        // $rp = new Replacement;
        // $rp->add("/%module%/", $setting["application_dir"]."/Packages/Modules");
        // $rp->add("/%lib%/", $setting["lib_dir"]);
        // $dir = $rp->replace($dir);
        
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
            null,
            [
                "install-token" => $token
            ]
        );
        FtpHelper::RmFile($h, $install);
        ftp_close($h);
        Logger::info("response");
        if ($response){
            Logger::warn($response);
        }
    }
    /**
     * execute command
     * @param mixed $command 
     * @param mixed $args 
     * @return never 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function exec($command, ...$args) { 

        $this->syncScriptCommand($command, "install-composer.php", ["args"=>$args]);
 
    }

}