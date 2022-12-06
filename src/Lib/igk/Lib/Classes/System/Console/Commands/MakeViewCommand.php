<?php
// @author: C.A.D. BONDJE DOUE
// @filename: MakeViewCommand.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\System\Console\Commands;

use IGK\System\Console\App;
use IGK\System\Console\AppCommand;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;
use ControllerInitListener;
use IGK\Helper\IO as IGKIO;
use \ApplicationController;
use \IGKControllerManagerObject;
 
class MakeViewCommand extends AppExecCommand{
    var $command = "--make:view"; 
 
    var $category = "make";

    var $desc  = "make new project's view";

    var $options = [
        "--action"=>"enable action",
        "--dir"=>"enable view dir"
    ]; 
    public function exec($command, $controller="", $viewname=""){
        if (empty($controller)){
            return false;
        } 
        if (empty($viewname)){
            Logger::danger("view name required");
            return false;
        } 
        Logger::info("make view ...".$controller);
        $author = $this->getAuthor($command);
                   
        $action = property_exists($command->options, "--action");
        $is_dir = property_exists($command->options, "--dir");
        $ctrl = igk_getctrl(str_replace("/", "\\", $controller), false);
        if (!$ctrl){
            Logger::danger("controller $controller not found");
            return false;
        }
  
        $dir = $ctrl->getViewDir();
        if ($is_dir){
           $dir .=  "/$viewname";
           $viewname =  IGK_DEFAULT_VIEW; 
        }  
        if (($ext = igk_io_path_ext($viewname)) == "phtml"){
            $viewname = igk_io_remove_ext($viewname);
        }

        $bind = [];

        $bind[$dir."/{$viewname}".IGK_VIEW_FILE_EXT] = function($file)use($viewname, $author){           
            $builder = new PHPScriptBuilder();
            $fname = $viewname.IGK_VIEW_FILE_EXT;
            $builder->type("function")->name($viewname)
            ->author($author)
            ->defs("\$t->clearChilds();")
            ->doc("view entry point")
            ->file($fname)
            ->desc("view ".$viewname);
            igk_io_w2file( $file,  $builder->render());
        };

       

        foreach($bind as $n=>$c){
            if (!file_exists($n)){
                $c($n, $command);
                Logger::info("generate : ".$n);
            }
        }
        
        \IGK\Helper\SysUtils::ClearCache(); 
        Logger::success("done\n");
    }
    public function help(){ 
        Logger::print("-");
        Logger::info("Make new Balafon's PROJECT view");
        Logger::print("-\n");
        Logger::print("Usage : ". App::Gets(App::GREEN, $this->command). " controller name [options]" );
        Logger::print("\n\n");
    }
}