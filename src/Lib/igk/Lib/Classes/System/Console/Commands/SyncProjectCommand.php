<?php

// @author: C.A.D. BONDJE DOUE
// @filename: SyncProjectCommand.php
// @date: 20220502 12:51:36
// @desc: sync project to an througth ftp 
namespace IGK\System\Console\Commands;

use Exception;
use IGK\Controllers\BaseController;
use IGK\Helper\BackupUtility;
use IGK\Helper\FtpHelper;
use IGK\Helper\IO;
use IGK\System\Console\App;
use IGK\System\Console\Commands\Sync\SyncProjectSettings;
use IGK\System\Console\Logger;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\IO\Path;
use IGK\System\Regex\Replacement;
use IGKException;
use ReflectionException;

/**
 * sync ftp project
 * @package IGK\System\Console\Commands
 */
class SyncProjectCommand extends SyncAppExecCommandBase
{
    var $command = "--sync:project";
    var $desc = "sync project through ftp configuration";
    var $category = "sync";
    var $help = "ftp sync project";
    var $options = [
        '--[list|restore[:foldername]] [--clearcache] [--no-zip]' => '',
        '--comment:[_litteral_]' => 'comment litteral to pass when backup the project',
        '--file:[single-file]' => 'sync single file'
    ];
    /**
     * use zip to indicate 
     * @var bool
     */
    var $use_zip;
    private $remove_cache = false;

    public function syncSingleFile($command, $project, $setting){
        $rf = igk_getv($command->options, "--file");
        if (!is_array($rf)){
            $rf = [$rf];
        }
        $dir = $project->getDeclaredDir();
        if (!is_object($h = $this->connect($setting["server"], $setting["user"], $setting["password"]))) {
            return $h;
        }
        $o_dir = self::_GetOutputdir($setting, $h, $project);
        // $path_key = self::PROJECT_DIR;
        // if (is_null($setting[$path_key])) {
        //     igk_die("[project_dir] is required");
        // }
        // $g = ftp_nlist($h, $setting[$path_key]);
        // $o_dir = $setting[$path_key] . "/" . $project;

        $files = [];
        while(count($rf)>0){
            $q = array_shift($rf);
            if (!$q)continue;
            if (file_exists($f = $dir."/".$q) || file_exists($f = $q)){
               $files[] = $f;
            }   
        }
        $pdir = $dir;
        $cdir = [];
        $project = basename($dir);
        $files && self::SyncFiles($files, $o_dir, $h,  $pdir,  $cdir, $project);
        ftp_close($h);
        return true;
    }
    private function _GetOutputdir($setting, $h, $ctrl){
        $path_key = self::PROJECT_DIR;
        if (is_null($setting[$path_key])) {
            igk_die("[project_dir] is required");
        }
        $project = basename($ctrl->getDeclaredDir());
        $g = ftp_nlist($h, $setting[$path_key]);
        $o_dir = $setting[$path_key] . "/" . $project;
        return $o_dir;
    }

    public function exec($command, ?string $project = null)
    {
        if (($c = $this->initSyncSetting($command, $setting)) && !$setting) {
            return $c;
        }




        $exclude_file_extension = "vscode|balafon|DS_Store|gkds";
        $options = igk_getv($command, "options");
        $arg =  property_exists($options, "--list") ? "l" : (property_exists($options, "--restore") ? "r" :
            "");



        if (is_null($project)) {
            Logger::danger("project name or controller is required");
            return -1;
        }
        $this->remove_cache = property_exists($options, "--clearcache");
        $this->use_zip = !property_exists($options, "--no-zip");
        
        $pdir = null;
        if ($ctrl = self::GetController($project, false)) {
            if (BaseController::IsSysController($ctrl)) {
                Logger::danger("can't sync system controller");
                return -3;
            }
            $pdir = $ctrl->getDeclaredDir();
        } else {
            if (!is_dir($pdir = igk_io_projectdir() . "/{$project}")) {
                Logger::danger("project not found");
                return -2;
            }
        }
        // for single file
        if ($ctrl && property_exists($command->options, '--file')){
            return $this->syncSingleFile($command, $ctrl, $setting);
        }
        $pdir = IO::GetUnixPath($pdir, true);
        $project = basename($pdir);
        if (!is_object($h = $this->connect($setting["server"], $setting["user"], $setting["password"]))) {
            return $h;
        }
        switch ($arg) {
            case "l":
                // list release
                $this->_listRelease($h, $setting[self::RELEASE_DIR], $project);
                break;
            case "r":
                $this->_restoreRelease($h, $project, $setting);
                break;
            default:
                $resolv_files = null;
                if ($ctrl) {
                    Logger::info('backup project before upload .... ');
                    $comment = igk_getv($command->options, '--comment');
                    BackupUtility::BackupProject($ctrl, $comment);
                    if ($cl  = $ctrl->resolveClass(\System\Console\Commands\SyncProject::class)) {
                        $resolv_files = new $cl();
                    }
                }


                SyncProjectSettings::InitProjectExcludeDir($pdir, $excludedir);

                if ($this->use_zip) {
                    $controller = null;
                    // get project in pdir
                    if (empty($pdir) && is_null($ctrl)) {
                        foreach (igk_sys_project_controllers() as $c) {
                            if ($pdir == $c->getDeclaredDir()) {
                                $controller = $c;
                                break;
                            }
                        }
                    } else {
                        $controller = $ctrl;
                    }
                    if (!is_null($controller)) {
                        $this->_installZipProject(
                            $controller,
                            Replacement::RegexExpressionFromString(implode("|", array_keys($excludedir))),
                            $h,
                            $setting
                        );
                    } else {
                        Logger::danger(sprintf("no controller found in : %s", $pdir));
                    }
                } else {

                    // sync project
                    $path_key = self::PROJECT_DIR;
                    if (is_null($setting[$path_key])) {
                        igk_die("[project_dir] is required");
                    }


                    $g = ftp_nlist($h, $setting[$path_key]);
                    $o_dir = $setting[$path_key] . "/" . $project;
                    if (!in_array($project, $g)) {
                        // upload project if not found
                        Logger::info("project not found in " . $setting["server"]);
                    } else {
                        $rsdir = $setting[self::RELEASE_DIR];
                        // move current folder to release
                        ftpHelper::CreateDir($h, $bckdir = $rsdir . "/" . $project . date("Ymd"));
                        Logger::info("rename " . $o_dir . " " . $bckdir);
                        ftpHelper::RenameFile($h, $o_dir, $bckdir);
                    }
                    ftpHelper::CreateDir($h, $setting[$path_key]);
                    ftp_chdir($h, $setting[$path_key]);
                    if (@ftp_mkdir($h, $project) === false) {
                        error_clear_last();
                    }
                    $cdir = [];
                    $excludedir = \IGK\Helper\Project::IgnoreDefaultDir();
                    // + | check 
                    if (file_exists($fc = Path::Combine($pdir, '.balafon-sync.project.json'))) {
                        $g = SyncProjectSettings::Load(json_decode(file_get_contents($fc)));
                        if ($g->ignoredirs) {
                            $v_ignores =  array_fill_keys($g->ignoredirs, 1);
                            $excludedir = array_merge($excludedir, $v_ignores);
                        }
                    }

                    $fc = function ($f, array &$excludedir = null) use ($exclude_file_extension, $resolv_files) {
                        $dir = dirname($f);
                        $basename = basename($f);
                        if ($resolv_files) {
                            if ($resolv_files->ignore($f)) {
                                return false;
                            }
                        }
                        if (preg_match("/^(~)/", $basename)) {
                            return false;
                        }
                        if ($excludedir) {
                            if (in_array($dir, $excludedir) || in_array(basename($dir), $excludedir)) {
                                $excludedir[] = $dir;
                                return false;
                            }
                        }
                        if (preg_match("#\.(git(.+)?|" . $exclude_file_extension . ")$#", $dir)) {
                            return false;
                        }
                        if (preg_match(
                            "#(phpunit(.+(\.(yml|dist)))|\/node_modules\/|\.(vscode|balafon|DS_Store|git(.+)))|\.(gkds)$#",
                            $f
                        )) {
                            return false;
                        }
                        return 1;
                    };
                    $v_files = IO::GetFiles($pdir, $fc, true, $excludedir);
                    self::SyncFiles($v_files, $o_dir,  $h,  $pdir,  $cdir, $project);
                    $this->removeCache($h, $setting[self::APP_DIR]);
                    Logger::success("sync project ... " . $o_dir);
                }
                break;
        }
        ftp_close($h);
        error_clear_last();
    }
    static function SyncFiles($v_files, $o_dir,  $h , $pdir,  & $cdir, string $project){

        foreach ($v_files as $f) {
            $g = substr($f, strlen($pdir));
            if ((($_cdir = dirname($g)) != "/") && !in_array($_cdir, $cdir)) {
                ftpHelper::CreateDir($h, dirname($project . $g));
                array_push($cdir, $_cdir);
            }
            Logger::print("upload : " . $f);
            $tfile =  $o_dir . $g;   

            $rf = FtpHelper::CreateDir($h, dirname($tfile)); 
            if (!ftp_put($h, $o_dir . $g, $f, FTP_BINARY)) {
                Logger::danger("failed to upload :  " . $f);
            }
        }
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
        $path = $setting[self::RELEASE_DIR];
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
                    FtpHelper::RenameFile($ftp, $path . "/" . $g, $target);
                } else {
                    Logger::danger("failed to remove $target");
                }
            }
            $this->removeCache($ftp, $setting["application_dir"]);
        }
    }
    // + | sync by zip controller project
    /**
     * zip project by zip install 
     * @param mixed $controller 
     * @param mixed $exclude 
     * @return string 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     * @throws Exception 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    private function _installZipProject($controller, $exclude, $h, $setting)
    {
        $file = tempnam(sys_get_temp_dir(), "blf");
        Logger::info("zip project : " . $controller->getName());
        igk_sys_zip_project($controller, $file, $exclude);
        rename($file, $file .= '.zip');

        $archive = "install_project.zip";
        // $file = '/private/var/folders/sp/f7bfk6cx359b61kd9cfp414h0000gn/T/blfqjEpbP.zip';
        Logger::info("archive : " . $file);
        if ($sb = $this->_getInstallScript($token, $archive)) {
            $script_install = igk_io_sys_tempnam("blf_project");
            igk_io_w2file($script_install, $sb);
            self::SyncAndInstall(
                $h,
                basename($controller->getDeclaredDir()),
                igk_str_snake(basename(igk_dir(get_class($controller)))),
                $archive,
                $file,
                $setting,
                $token,
                $script_install
            );
            unlink($script_install);
        }

        Logger::info("done : " . $file);
        return $file;
    }

    private function _getInstallScript(&$token, $name)
    {
        return self::GetScriptInstall(
            [
                "installer-helper.pinc", // entry helper
                "installer.helper.pinc", // class
                'install.project.script.pinc'
            ],
            $token,
            $name
        );
    }
    /**
     * Sync and and install 
     * @param mixed $h ftp resource
     * @param mixed $name name
     * @param mixed $file zip file
     * @param mixed $setting config setting
     * @param string $token token
     * @param string $script_install install source file 
     * @return void 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */
    public static function SyncAndInstall(
        $h,
        $project_name,
        $project_entry,
        $name,
        $file,
        $setting,
        string $token,
        string $script_install,
        $install_path_name = 'install_script.php'
    ) {

        $pdir = $setting["public_dir"];
        $uri = $setting["site_uri"];
        $install_dir =  $setting["application_dir"] . "/Projects";

        Logger::info("sync to : " . $pdir);
        Logger::print("upload project archive");
        ftp_put($h, $lib =  $pdir . "/" . ltrim($name,'/'), $file, FTP_BINARY);
        Logger::print("upload install script");
        ftp_put($h, $install = $pdir . "/" . $install_path_name, $script_install, FTP_BINARY);
        $response = igk_curl_post_uri(
            $uri . "/" . $install_path_name,
            [
                "install_dir" => $install_dir,
                "token" => $token,
                "app_dir" => $setting["application_dir"],
                "project_folder" => $project_name,
                "project_entry" => $project_entry,
                "archive" => $name,
                "home_dir" => igk_getv($setting, "home_dir")
            ],
            null,
            [
                "install-token" => $token,
                "archive" => $name,
            ]
        );


        // FtpHelper::RmFile($h, $lib);
        // FtpHelper::RmFile($h, $install);

        if (($status = igk_curl_status()) == 200) {
            Logger::info("curl response \n" . App::Gets(App::BLUE, $response));
            $rep = json_decode($response);
            if ($rep && !$rep->error) {
                Logger::success("install complete");
            }
        } else {
            Logger::danger("install script failed");
            Logger::warn("status : " . $status);
            Logger::warn($response);
        }
    }

    public function showUsage()
    {
        parent::showUsage();
        Logger::print("--sync:project controller|project [--name:ref-config-name]");
    }
}
