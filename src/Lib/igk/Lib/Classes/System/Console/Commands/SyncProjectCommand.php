<?php

// @author: C.A.D. BONDJE DOUE
// @filename: SyncProjectCommand.php
// @date: 20220502 12:51:36
// @desc: sync project to an througth ftp 
namespace IGK\System\Console\Commands;

use IGK\Controllers\BaseController;
use IGK\Helper\FtpHelper;
use IGK\Helper\IO;
use IGK\System\Console\Logger;

/**
 * sync ftp project
 * @package IGK\System\Console\Commands
 */
class SyncProjectCommand extends SyncAppExecCommandBase
{
    var $command = "--sync:project";
    var $desc = "sync project through ftp configuration";
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


        if (is_null($module)) {
            Logger::danger("project name is required");
            return -1;
        }
        if (!is_dir($pdir = igk_io_projectdir() . "/${module}")) {
            Logger::danger("project not found");
            return -2;
        }
        $pdir = IO::GetUnixPath($pdir, true);
        $module = basename($pdir);
        
        if (!is_object($h = $this->connect($setting["server"], $setting["user"], $setting["password"]))) {
            return $h;
        }
        switch ($arg) {
            case "l":
                // list release
                $this->_listRelease($h, $setting["release"], $module);
                break;
            case "r":
                $this->_restoreRelease($h, $module, $setting);
                break;
            default:
            if ($this->use_zip){
                $controller = null;
                $this->_installZipProject($controller);
               
            } else {
             
                // sync project
                $exclude = [];
                $g = ftp_nlist($h, $setting["path"]);
                $o_dir = $setting["path"] . "/" . $module;
                if (!in_array($module, $g)) {
                    // upload project if not found
                    Logger::info("project not found in " . $setting["server"]);
                } else {
                    // move current folder to release
                    ftpHelper::CreateDir($h, $bckdir = $setting["release"] . "/" . $module . date("YmdHis"));
                    Logger::info("rename " . $o_dir . " " . $bckdir);
                    ftp_rename($h, $o_dir, $bckdir);
                }
                ftpHelper::CreateDir($h, $setting["path"]);
                ftp_chdir($h, $setting["path"]);
                @ftp_mkdir($h, $module);
                $cdir = [];

                $fc = function ($f, array &$excludedir = null) {
                    $dir = dirname($f);
                    if ($excludedir) {
                        if (in_array($dir, $excludedir) || in_array(basename($dir), $excludedir)) {
                            $excludedir[] = $dir;
                            return false;
                        }
                    }
                    if (preg_match("#\.(git(.+)?|vscode|balafon|DS_Store)$#", $dir)) {
                        return false;
                    }
                    if (preg_match("#(phpunit(.+(\.(yml|dist))$)|\.(git(.+)?|vscode|balafon)$)#", $f)) {
                        return false;
                    }
                    return 1;
                };

                foreach (IO::GetFiles($pdir, $fc, true, $exclude) as $f) {

                    $g = substr($f, strlen($pdir));
                    if ((($_cdir = dirname($g)) != "/") && !in_array($_cdir, $cdir)) {
                        ftpHelper::CreateDir($h, dirname($module . $g));
                        array_push($cdir, $_cdir);
                    }
                    Logger::print("upload : " . $f);
                    ftp_put($h, $o_dir . $g, $f, FTP_BINARY);
                }
                $this->removeCache($h, $setting["application_dir"]);

                Logger::success("sync project ... " . $o_dir);
            }
                break;
        }
        ftp_close($h);
        error_clear_last();
    }
    protected function removeCache($ftp, $app_dir)
    {
        if ($this->remove_cache) {
            parent::removeCache($ftp, $app_dir . "/.Caches");
        }
    }

    private function  _listRelease($ftp, $path, $project)
    {
        $bckdir = $path;
        if (!@ftp_chdir($ftp, $bckdir)) {
            Logger::info("no release folder found");
        } else {
            $g = $this->_getRelease($ftp, $project);
            array_map(function ($i) {
                Logger::info($i);
            }, $g);
            return $g;
        }
    }
    private function _getRelease($ftp, $project)
    {
        $g = array_filter($m = ftp_nlist($ftp, ""), function ($i) use ($project) {
            if (preg_match("/^" . $project . "[0-9]+/", $i)) {
                return true;
            }
            return false;
        });
        sort($g);
        return $g;
    }

    /**
     * restore project release ... 
     * @param mixed $ftp 
     * @param mixed $project 
     * @param array $setting 
     * @return void 
     */
    private function _restoreRelease($ftp, $project, array $setting)
    {

        $projectPath = $setting["path"];
        $path = $setting["release"];
        $bckdir = $path;
        if (!@ftp_chdir($ftp, $bckdir)) {
            Logger::info("no release folder found");
        } else {
            if ($g = $this->_getRelease($ftp, $project)) {
                $g = array_shift($g);

                $target = $projectPath . "/" . $project;
                // $cwd = ftp_pwd($ftp);  
                Logger::info("remove $target");
                if (ftpHelper::RmDir($ftp, $target)) {
                    Logger::success("rename $path/$g to $target");
                    ftp_rename($ftp, $path . "/" . $g, $target);
                } else {
                    Logger::danger("failed to remove $target");
                }
            }
            $this->removeCache($ftp, $setting["application_dir"]);
        }
    }
    //zip controller project
    private function _installZipProject($controller){        
        $file = tempnam(sys_get_temp_dir(), "blf");
        igk_sys_zip_project($controller, $file);
        Logger::info("done : ".$file);
        return $file;
    }

}
