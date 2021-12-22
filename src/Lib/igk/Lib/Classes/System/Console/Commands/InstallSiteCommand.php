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
use \IGKControllerManagerObject;
 
class InstallSiteCommand extends AppExecCommand{
    var $command = "--install-site"; 
 
    var $category = "make";

    var $desc  = "create new site";

    var $options = [
        "--application:[dir]"=>"application directory",
        "--apache:[host_dir]"=>"apache server vitual host directory",
        "--environment:[key=v]"=>"define environment",
        "--listen:[port]"=>"port to listen. --listen:4000",
        "--module:[dir]"=>"module directory",
        "--package:[dir]"=>"package directory",
        "--session:[dir]"=>"session temp directory",
        "--usergroup:[u:group]"=>"set user and group. --usergroup:\"www-data:www-data\"",
        "-init"=>"init flag balafon configuration"
    ]; 
    public function exec($command, $document_root="", $viewname=""){  
        if (empty($document_root)){
            return false;
        } 
        $cnf = $command->app->getConfigs();
        $author =igk_getv($command->options, "--author",  $cnf ? $cnf->get("author", IGK_AUTHOR) : IGK_AUTHOR);  
        $module =  igk_getv($command->options, "--module", null);
        $package = igk_getv($command->options, "--package",  null);
        $session = igk_getv($command->options, "--session",  null);
        $appdir = igk_getv($command->options, "--appdir",  null);
        $projects = igk_getv($command->options, "--projects",  null);
        $environment = igk_getv($command->options, "--environment", "development");
        $listen = igk_getv($command->options, "--listen", 80);
        $apachedir = igk_getv($command->options, "--apache", null); 
        $ugroup = igk_getv($command->options, "--usergroup", "www-data:www-data");
        $init = property_exists($command->options, "-init");
       
        if ($document_root!="."){
            if (!IO::CreateDir($document_root)){
                Logger::danger("directory creation failed");
                return -1;
            }
            $document_root = realpath($document_root);
        }
        chdir($document_root); 
        if (InstallSite::Install($document_root, $listen, $environment,
            [
                "sessiondir"=>$session,
                "projectdir"=>$projects,
                "appdir"=>$appdir,
                "packagedir"=>$package,
                "moduledir"=>$module,
                "apachedir"=>$apachedir,
            ])){
                if (get_current_user()=="root"){
                    `chown -R {$ugroup} *`;
                    `chmod -R 775 *`;
                }


        if ($init){ 
            igk_environment()->balafon_author = $author;
            $command->app::Exec($command->app, ["--init"]);
        }
        Logger::success("install a site at ".$document_root."\n");
        Logger::warn("Please if you target apache web server please restart it.");
        Logger::warn("Navigate to : http://localhost".($listen?":".$listen:""));

        } else {
            Logger::danger("failed to install site ".$document_root."\n");
        }
    }
    public function help(){   
        Logger::print("-");
        Logger::info("Install site");
        Logger::print("-\n");
        Logger::print("Usage : ". App::gets(App::GREEN, $this->command). 
        " base_source_dir [options]"         
    );
        Logger::print("\n\n");
        $this->showOptions();
    }
}