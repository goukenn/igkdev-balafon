<?php
// @author: C.A.D. BONDJE DOUE
// @filename: MakeManifest.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\System\Console\Commands;

use IGK\System\Console\App;
use IGK\System\Console\AppCommand;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;
use IGKActionBase;
use ControllerInitListener;
use IGK\Helper\IO as IGKIO;
use \ApplicationController;
use \IGKControllerManagerObject;
 
class MakeManifestCommand extends AppExecCommand{
    var $command = "--make:manifest"; 
 
    var $category = "make";

    var $desc = "make new project's manifest";

    var $options = [
        "--name"=>"display name of the application",
        "--force"=>"force generation" 
    ]; 
    public function exec($command, $controller=""){
        $pwa = igk_require_module("igk/PWA");
        if (empty($controller)){
            return false;
        } 
       
        Logger::info("make manifest ...");
        $author = $command->app->getConfigs()->get("author", IGK_AUTHOR);
        $type = igk_str_ns(igk_getv($command->options, "--type", IGKActionBase::class));
        $is_force = property_exists($command->options, "--force");
          
        $ctrl = igk_getctrl(str_replace("/", "\\", $controller), false);
        if (!$ctrl){
            Logger::danger("controller $controller not found");
            return false;
        }
        $controller = igk_str_ns(igk_getv($command->options, "--name", $ctrl->getName()));
        $shortname = igk_str_ns(igk_getv($command->options, "--shortname", $controller));
        $version = igk_str_ns(igk_getv($command->options, "--version", "1.0"));
        
        $ns = $ctrl->getEntryNamespace();
        $dir = $ctrl->getDeclaredDir(); 
        $info = new \IGK\PWA\IGKPWA($controller, "/");
        $info->info->short_name = $shortname;
        $info->info->version = $version;
        
        $info->addIcon("256x256", $ctrl::asset("Img/PWA_ICONS.png"));
 

        $bind[$dir."/manifest.json"] = function($file)use($info){               
            igk_io_w2file( $file,  json_encode($info->info, JSON_PRETTY_PRINT));// $builder->render());
        };
        foreach($bind as $n=>$c){
            if ($is_force || !file_exists($n)){
                $c($n, $command);
                Logger::info("generate : ".$n);
            }
        }
        IGKControllerManagerObject::ClearCache(); 
        Logger::success("done\n");
    }
    public function help(){
        Logger::print("-");
        Logger::info("Make new Balafon's PROJECT action");
        Logger::print("-\n");
        Logger::print("Usage : ". App::gets(App::GREEN, $this->command). " ctrl name [options]" );
        Logger::print("\n\n");
    }
}