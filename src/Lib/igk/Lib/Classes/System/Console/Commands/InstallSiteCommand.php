<?php

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
use \IGKControllerManagerObject;
 
class InstallSiteCommand extends AppExecCommand{
    var $command = "--install-site"; 
 
    var $category = "make";

    var $desc  = "create new site";

    var $options = [
        "--root_dir:[dir]"=>"document root. default is current install directory",
        "--apache:[host_dir]"=>"apache server vitual host directory",
        "--environment:[key=v]"=>"define environment",
        "--listen:[port]"=>"port to listen. default is port 80. --listen:4000",
        "--application:[dir]"=>"application directory",
        "--module:[dir]"=>"module directory",
        "--package:[dir]"=>"package directory",
        "--session:[dir]"=>"session temp directory",
        "--usergroup:[u:group]"=>"set user and group. --usergroup:\"www-data:www-data\"",
        "-init"=>"init flag balafon configuration"
    ]; 
    public function exec($command, $install_dir="", $viewname=""){  
        $force = property_exists($command->options, "--force");
        if (empty($install_dir)){
            $install_dir = getcwd(); 
            $force = 1;
        }  else {
            $install_dir = realpath($install_dir);
        }
        if ($install_dir!="."){
            if (!IO::CreateDir($install_dir)){
                Logger::danger("directory creation failed");
                return -1;
            }
            $install_dir = realpath($install_dir);
        }
        

        $cnf = $command->app->getConfigs();
        // echo ("project dir ::::: " . json_encode((array)$cnf));
        // exit;
        $author =igk_getv($command->options, "--author",  $cnf ? $cnf->get("author", IGK_AUTHOR) : IGK_AUTHOR);  
        $module =  igk_getv($command->options, "--module", null);
        $package = igk_getv($command->options, "--package",  null);
        $session = igk_getv($command->options, "--session",  null);
        $appdir = igk_getv($command->options, "--appdir",  null);
        $projects = igk_getv($command->options, "--projects", null);
        $environment = igk_getv($command->options, "--environment", "development");
        $listen = igk_getv($command->options, "--listen", 80);
        $apachedir = igk_getv($command->options, "--apache", null); 
        $ugroup = igk_getv($command->options, "--usergroup", "www-data:www-data");
        $init = property_exists($command->options, "-init");
        $root_dir = igk_getv($command->options, "--root_dir", null); 
        $is_primary = 1;
        if (empty($root_dir)){
            $root_dir = $install_dir;
        }
        else {
            $root_dir = StringUtility::Uri($install_dir."/".ltrim($root_dir, "/"));
            $is_primary = 0;
        }
        chdir($install_dir); 

        // IGKIO::RmDir($install_dir); 
        
        if (InstallSite::Install($install_dir, $listen, $environment,
            [
                "rootdir"=>$root_dir,
                "sessiondir"=>$session,
                "projectdir"=>$projects,
                "appdir"=>$appdir,
                "packagedir"=>$package,
                "moduledir"=>$module,
                "apachedir"=>$apachedir,
                "force"=>$force,
                "user:group"=>$ugroup,
                "is_primary"=>$is_primary
            ])){
                if (igk_environment()->isUnix() && (get_current_user()=="root")){
                    `chown -R {$ugroup} *`;
                    `chmod -R 775 *`;
                }


        if ($init){ 
            igk_environment()->balafon_author = $author;
            $command->app::Exec($command->app, ["--init"]);
        }
        Logger::success("install a site at ".$install_dir."\n");
        Logger::warn("Please if you target apache web server please restart it.");
        Logger::warn("Navigate to : http://localhost".($listen?":".$listen:""));

        } else {
            Logger::danger("failed to install site ".$install_dir."\n");
        }
    }
    public function help(){   
        Logger::print("-");
        Logger::info("Install site");
        Logger::print("-\n");
        Logger::print("Usage : ". App::gets(App::GREEN, $this->command). 
        " install_directory [options]"         
    );
        Logger::print("\n\n");
        $this->showOptions();
    }
}