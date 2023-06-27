<?php

// @author: C.A.D. BONDJE DOUE
// @filename: MakeDbMacrosCommand.php
// @date: 20230203 16:10:17
// @desc: 


namespace IGK\System\Console\Commands;

use IGK\System\Console\App;
use IGK\System\Console\AppCommand;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\Actions\ActionBase;
use IGKActionBase;
use ControllerInitListener;
use IGK\Helper\IO as IGKIO;
use \ApplicationController;
use IGK\Actions\MiddlewireActionBase;
use IGK\Controllers\SysDbController;
use IGK\Helper\StringUtility;
use IGK\Helper\Utility;
use igk\System\Console\Commands\Utility as CommandsUtility;
use \IGKControllerManagerObject;
 
class MakeDbMacrosCommand extends AppExecCommand{
    var $command = "--make:db-macros"; 
 
    var $category = "make";

    var $desc = "make model's macros class";

    var $options = [ 
        "--type"=>"defaut Model type class", 
        "--clearcache"=>"clear cache",
        "--force"=>"destroy existing macros if exists",
    ]; 

    var $help = "[options] controller macrosName";

    /**
     * 
     * @var callable
     */
    var $definition; // definition callback

    /**
     * array of uses
     * @var ?array|?string
     */
    var $uses;
    /**
     * @var string $controller Controller
     * @var string $actionName the action to create 
     */
    public function exec($command, $controller="", $macroName=""){
        if (empty($controller)){
            Logger::danger("controller is required");
            return false;
        } 
        if (empty($macroName)){
            Logger::danger("macro name name required");
            return false;
        } 
        if ($controller=='%sys%'){
            $controller = SysDbController::ctrl();
        }
        Logger::info("make macros ...".$controller);
        $author = $this->getAuthor($command);
        $type = igk_str_ns(igk_getv($command->options, "--type", ActionBase::class));
        $type = igk_getv([
            "project"=>ProjectDefaultAction::class,
            "def"=>ActionBase::class,
            "middlewire"=>MiddlewireActionBase::class
        ], strtolower($type), $type);
        
        $ctrl = igk_getctrl(str_replace("/", "\\", $controller), false);
        if (!$ctrl){
            Logger::danger("controller $controller not found");
            return false;
        }
        $ctrl::register_autoload();
         
        $ns = $ctrl->getEntryNamespace();
        $dir = $ctrl::classdir(); 
        $bind = [];
        $macroName = ucfirst($macroName);
        if ((($pos = strrpos(strtolower($macroName), 'macros'))>0) && (($pos+6)==strlen($macroName))){
            $macroName = substr($macroName,0, -6);
        }

        $path = $macroName;
        $tcl =  explode("/", StringUtility::Uri($path ));
        array_pop( $tcl); 
        if (!empty($ns)){
            $ns.="\\";
        }
        $ns .= \Database\Macros::class;
        if (count($tcl)){
            $ns.= "\\".implode("\\", $tcl);
        }
        // + | --------------------------------------------------------------------
        // + | add used model
        // + |
        
        $this->uses = function()use($path, $ctrl){
            $m = $ctrl->resolveClass("Models\\{$path}");
            return array_filter([
                $m
            ]);
        };
         
        $bind[$dir."/Database/Macros/{$path}Macros.php"] = function($file)use($macroName, 
            $author, $ns){          
            $content = $this->_getContent(); 
            $v_uses = $this->_getUses() ?? [];
            $builder = new PHPScriptBuilder();
            $fname = $macroName.IGK_VIEW_FILE_EXT;           
            $builder->type("class")->name(igk_io_basenamewithoutext($file))
            ->class_modifier('abstract')
            ->uses($v_uses)
            ->author($author)
            ->namespace($ns)
            ->defs($content) 
            ->file($fname) 
            ->desc("macros for model ".$macroName);
            igk_io_w2file( $file,  $builder->render());
        };

        CommandsUtility::MakeBindFiles($command, $bind, property_exists($command->options, "--force"));
        if(property_exists($command->options, "--clearcache" ))
            \IGK\Helper\SysUtils::ClearCache(); 
        Logger::success("Done - Make Macros for model");
    }
    private function _getContent(){
        if ($def = $this->definition){
            return $def();
        }
        return "";
    }
    private function _getUses(){
        if ($uses = $this->uses){
            return $uses();
        }
        return [];
    }
    public function help(){
        Logger::print("-");
        Logger::info("Make db model macros");
        Logger::print("-\n");
        Logger::print("Usage : ". App::Gets(App::GREEN, $this->command). " controller name [options]" );
        Logger::print("\n\n");
    }
}