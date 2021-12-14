<?php

namespace IGK\System\Console\Commands;

use IGK\System\Console\App;
use IGK\System\Console\AppCommand;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;
use IGKCtrlInitListener;
use IGK\Helper\IO as IGKIO;
use \ApplicationController;
use \IGKControllerManagerObject;
use Illuminate\Support\Facades\Log;

class OutSideLinksCommand extends AppExecCommand{
    var $command = "--outsidelinks"; 
 
    var $category = "utility";

    var $desc  = "retrieve all outside links";

    var $options = [ 
    ]; 
    public function exec($command, $path=null, $viewname=""){
        if (empty($path)){
            $path = getcwd();
        } 
        $links = [];
        $file = $path;
        $tsourcedir = [$path];
        while($sourcedir = array_pop($tsourcedir)){
            if ($hdir = opendir($sourcedir)){
                while($c = readdir($hdir)){
                    if (($c==".") || ($c=="..")){
                        continue;
                    }
                    $mdir = $sourcedir.DIRECTORY_SEPARATOR.$c;
                    // Logger::print("resolv:".$mdir);                   
                    if (is_link($mdir) && empty(strstr($rp= realpath($mdir), $file)) && is_dir($rp)){
                        $links[] = $mdir;  
                        continue;                          
                    }
                    if (is_dir($mdir)){
                        array_push($tsourcedir, $mdir);
                    }
                }
                closedir($hdir);
            }  
        }        
        Logger::print(implode("\n", $links));        
        Logger::success("done\n");
    }
    public function help(){
        parent::help();
        Logger::print("-");
        Logger::info("Retrieve project outsie links");
        
    }
}