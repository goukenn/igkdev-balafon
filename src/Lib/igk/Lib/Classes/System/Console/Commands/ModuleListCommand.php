<?php

namespace IGK\System\Console\Commands;

use IGK\System\Console\AppCommand;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\Helper\IO as IGKIO;
use IGK\System\Console\App;
use ReflectionClass;

class ModuleListCommand extends AppExecCommand{
    var $command = "--module";
    var $category = "module";
    var $desc  = "module management command";

    public function exec($command, $args="ls"){
       $args = "ls";

       switch($args){
           default:
            $this->listCommand();
           break;
       }
    }
    private function listCommand(){
        $mod = igk_get_modules();
        if (!$mod  || (count($mod) == 0)){
            Logger::info(__("No module installed at ".igk_get_module_dir())); 
            return;
        }
        foreach($mod as $k=>$v){
            $tag = "\r\t\t";
            $f = $k;
            $f .= "\n".$tag.$v->author;
            $tag .= "\t\t\t";
            $f .= $tag.$v->version;
            $tag .= "\t";
            $mod = igk_get_module($k);
            if (!$mod){
                $f.= $tag.App::gets( App::RED, "module not found");
            }else {
                $f .= $tag.$mod->getDeclaredDir().":".$mod->config("entry_NS"); 
            }
            Logger::print($f); 
        }
    }
}