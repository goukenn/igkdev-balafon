<?php

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
use IGK\Actions\MiddlewireActionBase;
use IGK\Helper\StringUtility;
use IGK\Helper\Utility;
use igk\System\Console\Commands\Utility as CommandsUtility;
use \IGKControllerManagerObject;
 
class MakeActionCommand extends AppExecCommand{
    var $command = "--make:action"; 
 
    var $category = "make";

    var $desc = "make new project's action";

    var $options = [ 
        "--type"=>"defaut action type class",
        "--force"=>"force create action"
    ]; 

    var $help = "[options] controller actionName";

    /**
     * 
     * @var callable
     */
    var $definition; // definition callback
    /**
     * @var string $controller Controller
     * @var string $actionName the action to create 
     */
    public function exec($command, $controller="", $actionName=""){
        if (empty($controller)){
            return false;
        } 
        if (empty($actionName)){
            Logger::danger("action name required");
            return false;
        } 
        Logger::info("make action ...".$controller);
        $author = $command->app->getConfigs()->get("author", IGK_AUTHOR);
        $type = igk_str_ns(igk_getv($command->options, "--type", IGKActionBase::class));
        $type = igk_getv([
            "project"=>ProjectDefaultAction::class,
            "def"=>IGKActionBase::class,
            "middlewire"=>MiddlewireActionBase::class
        ], strtolower($type), $type);
        
        $ctrl = igk_getctrl(str_replace("/", "\\", $controller), false);
        if (!$ctrl){
            Logger::danger("controller $controller not found");
            return false;
        }
        if (!$type || !class_exists($type) || !(($type==IGKActionBase::class) || is_subclass_of($type, IGKActionBase::class))){
            Logger::danger("type class not found : [$type] ");
            return false;
        }
        $ns = $ctrl->getEntryNamespace();
        $dir = $ctrl::classdir(); 
        $bind = [];
        $actionName = ucfirst($actionName);
        $path = $actionName;
        $tcl =  explode("/", StringUtility::Uri($path ));
        array_pop( $tcl); 
        if (!empty($ns)){
            $ns.="\\";
        }
        $ns .= "Actions";
        if (count($tcl)){
            $ns.= "\\".implode("\\", $tcl);
        }
         
        $bind[$dir."/Actions/{$path}Action.php"] = function($file)use($actionName, 
            $author, $ns, $type){          
            $content = $this->_getContent(); 
            $builder = new PHPScriptBuilder();
            $fname = $actionName.".phtml";           
            $builder->type("class")->name(igk_io_basenamewithoutext($file))
            ->author($author)
            ->namespace($ns)
            ->defs($content)
            ->doc("view action")
            ->file($fname)
            ->extends($type)
            ->desc("view action ".$actionName);
            igk_io_w2file( $file,  $builder->render());
        };

        CommandsUtility::BindFiles($command, $bind, property_exists($command->options, "--force"));
        IGKControllerManagerObject::ClearCache(); 
        Logger::success("Done - Make Action");
    }
    private function _getContent(){
        if ($def = $this->definition){
            return $def();
        }
        return "";

    }
    public function help(){
        Logger::print("-");
        Logger::info("Make new Balafon's PROJECT action");
        Logger::print("-\n");
        Logger::print("Usage : ". App::gets(App::GREEN, $this->command). " ctrl name [options]" );
        Logger::print("\n\n");
    }
}