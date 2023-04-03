<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SyncModuleCommand.php
// @date: 20220729 13:58:01
// @desc: 

namespace IGK\System\Console\Commands;

use IGK\Controllers\ApplicationModuleController;
use IGK\Helper\FtpHelper;
use IGK\Helper\IO;
use IGK\System\Console\App;
use IGK\System\Console\Logger;
use IGK\System\Controllers\ApplicationModules;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\System\IO\StringBuilder;

/**
 * sync ftp project
 * @package IGK\System\Console\Commands
 */
class SyncModuleCommand extends SyncAppExecCommandBase
{
    var $command = "--sync:module";
    var $desc = "sync module through ftp configuration";
    var $category = "sync";
    var $help = "--[list|restore[:foldername] --clearcache  --zip";
    /**
     * use zip to indicate 
     * @var bool
     */
    var $use_zip;
    private $remove_cache = false;

    public function exec($command, ?string $module = null)
    {
        if (($c = $this->initSyncSetting($command, $setting)) && !$setting) {
            return $c;
        }

        $options = igk_getv($command, "options");
        $arg =  property_exists($options, "--list") ? "l" : (property_exists($options, "--restore") ? "r" :
            "");

        $this->remove_cache = property_exists($options, "--clearcache");
        $this->use_zip = property_exists($options, "--zip");


        if (!is_null($module)) {
            $app_module = str_replace(".", "/", $module);
            if (!($app_module = igk_require_module($module, null, null, 0))) {
                Logger::danger("module is missing");
                return -1;
            }
            $r = $this->sync_module($app_module, $setting);

            error_clear_last();
            return $r;
        }



        Logger::info("Sync all modules");
        if ($cache_file = ApplicationModules::GetCacheFile()){
        // remove json modules cache file files
            @unlink($cache_file);
        }

        $tab = igk_get_modules();

        if ($tab && ( ($count = count($tab)) > 0)) {
            $i = 0;
            foreach ($tab as $mod) {
                if ($module = igk_get_module($mod->name)){
                    Logger::info("sync : ". $i . " / ".$count);
                    // Logger::info("name: ".$mod->name);
                    $this->sync_module($module, $setting);
                }
                $i++;
            }
        }
        error_clear_last();
    }
    protected function sync_module(ApplicationModuleController $module, $setting)
    {

        if (!is_object($h = $this->connect($setting["server"], $setting["user"], $setting["password"]))) {
            return -2;
        }
        Logger::info("Sync module : " . $module->getName());
        $install_dir = $setting["module_dir"]; //igk_io_collapse_path($module->getDeclaredDir());
        if (empty($install_dir)){
            $dir = $setting["application_dir"] ?? igk_die("no application directory");
            $install_dir .= $dir."/Packages/Modules";
            // igk_die("install module directorty not set");
            
        }
        Logger::info("install_dir : " . $install_dir); 
        $file = tempnam(sys_get_temp_dir(), "blf");
        $script_install = igk_io_sys_tempnam("blf_module_script");
        igk_sys_zip_project($module, $file, null, IGK_AUTHOR, [
            "module_name" => $module->getName(),
            "module_version" => $module->getVersion(),
            "module_path" => igk_io_collapse_path($module->getDeclaredDir()),
        ]);
        rename($file, $file = $file . ".zip");
        Logger::info("done : " . $file);
        $token = null;
        $name = "module" . $module->getName() . ".zip";
        $sb = $this->_getInstallScript($token, $name);
  
        igk_io_w2file($script_install, $sb);

        $pdir = $setting["public_dir"];
        $uri = $setting["site_uri"];

        //updaload zip and install it 
        ftp_put($h, $lib =  $pdir . "/" . $name, $file, FTP_BINARY);
        ftp_put($h, $install = $pdir . "/install_module.php", $script_install, FTP_BINARY);
        unlink($file);
        unlink($script_install);


        $response = igk_curl_post_uri(
            $uri . "/install_module.php",
            [
                "install_dir" => $install_dir,
                "token" => $token,
                "app_dir" => $setting["application_dir"],
                "archive" => $name,
            ],
            null,
            [
                "install-token" => $token,
                "archive"=>$name,
            ]
        );


        FtpHelper::RmFile($h, $lib);
        FtpHelper::RmFile($h, $install);
        ftp_close($h);

        if (($status = igk_curl_status()) == 200) {
            Logger::info("curl response \n" . App::Gets(App::BLUE, $response));
            $rep = json_decode($response);
            if (!$rep->error) {
                Logger::success("install complete");
            }
        } else {
            Logger::danger("install script failed");
            Logger::warn("status : " . $status);
            Logger::warn($response);
        }
    }
    private function _getInstallScript(&$token, $name)
    {
        return self::GetScriptInstall('install.module.script.pinc', $token, $name);      
    }
    protected function removeCache($ftp, $app_dir)
    {
        if ($this->remove_cache) {
            parent::removeCache($ftp, $app_dir . "/.Caches");
        }
    }
}
