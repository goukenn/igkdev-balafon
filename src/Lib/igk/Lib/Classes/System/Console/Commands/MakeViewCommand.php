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
use IGK\Helper\ViewHelper;
use IGK\System\Console\Helper\ConsoleUtility;
use IGK\System\IO\FileHandler;
use \IGKControllerManagerObject;
 
class MakeViewCommand extends AppExecCommand{
    var $command = "--make:view"; 
 
    var $category = "make";

    var $desc  = "make new project's view";

    var $options = [
        "--controller:controller"=>"set controller to use",
        "--action"=>"enable action",
        "--dir"=>"enable view dir",
        "--force"=>"flag:force  file creation ",
        "--scaffold:[scaffoldtype]"=>"type of view to generate. default is null. or builder"
    ]; 
    public function exec($command, $controller=null, $viewname=""){
        $controller = $controller ?? igk_getv($command->options, "--controller");
        if (empty($controller)){
            Logger::danger("controller required");
            return false;
        } 
        if (empty($viewname)){
            Logger::danger("view name required");
            return false;
        } 
        Logger::info("make view for ... ".$controller);
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
            $viewname.=IGK_VIEW_FILE_EXT;
        } else 
        {
            $handlers = FileHandler::GetViewContextFileHandlers();

            if ($handlers && !in_array('.'.$ext, array_keys($handlers))){
                $viewname.='.phtml';
            }

        }

        $bind = [];
        $scaffold = igk_getv($command->options, '--scaffold');
        $force = property_exists($command->options, '--force');

        $bind[$dir."/{$viewname}"] = function($file)use($viewname, $author, $scaffold){  
            // TODO : FROM Scaffold generate the base document 
            $src = $this->getInitViewContent($viewname, $scaffold);
            $builder = new PHPScriptBuilder();
            $ext = igk_io_path_ext($viewname);
            if ($ext == 'phtml'){
                $fname = $viewname;
                $builder->type("function")->name($viewname)
                ->author($author)
                ->defs($src)
                ->docs("view entry point")
                ->file($fname)
                ->desc(implode("\n",["", " @view: ".$viewname]));
                igk_io_w2file( $file,  $builder->render());
            }else {
                $src = '';
                if(
                $handler = FileHandler::GetFileHandlerFromExtenstion('.'.$ext)){

                    $src = $handler->initDefaultSource();
                }
                igk_io_w2file($file, $src);
            }
        };

       

        ConsoleUtility::MakeFiles($bind, $command, $force);

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

    public function getInitViewContent(string $viewname, ?string $type=null):string{
        if ($type){
            if ($type == 'builder'){
                return "\$builder([\"View : $viewname\"]);";
            } else {
                // 
                if ($builder = ViewHelper::GetViewScaffold($type)){
                    return $builder->initView($viewname);
                }
                igk_die('missing scaffold type');
            }
        }

        return  "\$t->div()->Content = 'View : $viewname';";        
    }
}