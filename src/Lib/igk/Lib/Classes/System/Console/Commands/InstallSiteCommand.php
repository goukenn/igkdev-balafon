<?php
// @author: C.A.D. BONDJE DOUE
// @filename: InstallSiteCommand.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\System\Console\Commands;

use IGK\System\Console\App;
use IGK\System\Console\AppCommand;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Installers\InstallSite;
use IGK\System\IO\File\PHPScriptBuilder;
use ControllerInitListener;
use IGK\Helper\IO as IGKIO;
use \ApplicationController;
use IGK\Helper\IO;
use IGK\Helper\StringUtility;
use IGK\System\Installers\LaravelMixInstaller;
use IGKCaches;
use \IGKControllerManagerObject;
use IGKEvents;

class InstallSiteCommand extends AppExecCommand
{
    var $command = "--install-site";

    var $category = "sys";

    var $desc  = "create new site";

    var $options = [
        "--root-dir:[dir]" => "document root. default is current install directory. if relative, base on the install site",
        "--apache:[host_dir]" => "apache server vitual host directory",
        "--environment:[key=v]" => "define environment",
        "--listen:[port]" => "port to listen. default is port 80. --listen:4000",
        "--application:[dir]" => "application directory",
        "--module:[dir]" => "module directory",
        "--package:[dir]" => "package directory",
        "--session:[dir]" => "session temp directory",
        "--usergroup:[u:group]" => "set user and group. --usergroup:\"www-data:www-data\"",
        "--laravel-mix" => "flag enable laravel-mix",
        "--init-config" => "init flag balafon configuration",
        "--uri:[url]"=>"base uri",
        "--no-subdomain"=>"disable subdomain support",
        "--no-webconfig"=>"disable web configuration",
    ];
    public function exec($command, $install_dir = "", $viewname = "")
    {
        $force = property_exists($command->options, "--force");
        if (empty($install_dir)) {
            $install_dir = getcwd();
            $force = 1;
        } else {
            if (!is_dir(realpath($install_dir))) {
                IGKIO::CreateDir($install_dir);
                $force = 1;
            }
            $install_dir = realpath($install_dir);
        }
        if ($install_dir != ".") {
            if (!IO::CreateDir($install_dir)) {
                Logger::danger("directory creation failed");
                return -1;
            }
            $install_dir = realpath($install_dir);
        }


        $cnf = $command->app->getConfigs();

        $author = igk_getv($command->options, "--author",  $cnf ? $cnf->get("author", IGK_AUTHOR) : IGK_AUTHOR);
        $module =  igk_getv($command->options, "--module", null);
        $package = igk_getv($command->options, "--package",  null);
        $session = igk_getv($command->options, "--session",  null);
        $appdir = igk_getv($command->options, "--appdir",  null);
        $projects = igk_getv($command->options, "--projects", null);
        $environment = igk_getv($command->options, "--environment", igk_app()->getApplication()->environment ?? igk_environment()->name());
        $base_uri = igk_getv($command->options, "--uri", "localhost");

        $listen = igk_getv($command->options, "--listen", 80);
        $apachedir = igk_getv($command->options, "--apache", null);
        $ugroup = igk_getv($command->options, "--usergroup", null) ?? $this->getUserGroup();
        $init = property_exists($command->options, "--init-config");
        
 
        $root_dir = igk_getv($command->options, "--root-dir", null);
        $is_primary = 1;
        $cache_dir = igk_io_cachedir(); 

        if (empty($root_dir)) {
            $root_dir = $install_dir;
        } else {
            $root_dir = StringUtility::Uri($install_dir . "/" . ltrim($root_dir, "/"));
            $is_primary = 0;
        }
        chdir($install_dir);
        // + | ----------------------------------------------------------------------------
        // + | HANDLE SERVER HOOK
        // + |
        igk_reg_hook(IGKEvents::HOOK_INSTALL_SITE, [LaravelMixInstaller::class, "Handle"]);

        if (InstallSite::Install(
            $install_dir,
            $listen,
            $environment,
            [
                "rootdir" => $root_dir,
                "sessiondir" => $session,
                "projectdir" => $projects,
                "appdir" => $appdir,
                "packagedir" => $package,
                "moduledir" => $module,
                "apachedir" => $apachedir,
                "force" => $force,
                "user:group" => $ugroup,
                "is_primary" => $is_primary,
                "is_laravel_mix" => property_exists($command->options, "--laravel-mix"),
                "base_uri"=>$base_uri,
                // + | FLAG CONFIGURATION 
                "no_subdomain"=>property_exists($command->options, "--no-subdomain"),
                "no_webconfig"=>property_exists($command->options, "--no-webconfig"),
            ]
        )) {
            if (igk_environment()->isUnix() && (get_current_user() == "root")) {
                // + | setting user group
                if ($ugroup){
                    `chown -R {$ugroup} *`;
                }
                `chmod -R 775 *`;
            }
            if ($init) {
                // + | passing init command
                igk_environment()->balafon_author = $author;
                $i_cmd = ["--init"];
                if (property_exists($command->options, "--force")){
                    $i_cmd[] = "--force";
                }
                $command->app::Exec($command->app, $i_cmd);
            }
            Logger::success("install a site at " . $install_dir . "\n");
            Logger::warn("Please if you target apache web server please restart it.");
            Logger::warn("Navigate to : http://localhost" . ($listen ? ":" . $listen : ""));
            if (!$is_primary){
                //remove cachee directory for host
                IO::RmDir($cache_dir);
            }

        } else {
            Logger::danger("failed to install site " . $install_dir . "\n");
        }
    }
    public function help()
    {
        Logger::print("-");
        Logger::info("Install site");
        Logger::print("-\n");
        Logger::print(
            "Usage : " . App::Gets(App::GREEN, $this->command) .
                " install_directory [options]"
        );
        Logger::print("\n\n");
        $this->showOptions();
    }

    private function getUserGroup(){
        if (igk_environment()->isUnix()){
            $s = `groups`;
           
            if (preg_match("/wwww-data/", $s)){
                return "www-data:www-data";
            }
            if (preg_match("/_www/i",$s)){
                return "_www:_www";
            }
        }
    }
}
