<?php
 
 // @author: C.A.D. BONDJE DOUE
 // @filename: ProjectCheckViewCommand.php
 // @date: 20220830 14:52:41
 // @desc: check views 
 

namespace IGK\System\Console\Commands;

use Exception;
use IGK\Controllers\ControllerParams;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger; 
use IGK\System\Console\App;  

/**
 * list all project
 * @package IGK\System\Console\Commands
 */
class ProjectCheckViewCommand extends AppExecCommand{

    var $command = "--project:check-view";
    var $category = "project";
    var $desc = "check -lint all views";

    public function exec($command, $controller=null) { 
        
        if (is_null($controller)){
            return -1;
        }
        $controller = str_replace("/", "\\", $controller);
        if (!($ctrl = igk_getctrl($controller, 0))){
            Logger::danger("no controller found");
            return -2;
        }
        $withHidden = self::GetHasOptions($command, "-h|--withHidden") ?? false;

        // $file = $ctrl->getViewFile('.forms/enterprise');
        // $r = [];
        // igk_debug(1);
        // $file2 = $ctrl->getViewFile('default8.phtml', 1, $r); // $this->getViewFile($v, 1, $params)
        // igk_wln_e($file, $file2, "------------------");

        $views = $ctrl->getViews($withHidden);
        $viewDir = $ctrl->getViewDir();
        $ctrl->{ControllerParams::NoActionHandle} = 1;
        $ctrl->{ControllerParams::NoCompilation} = 1;
        $ctrl->{ControllerParams::AllowHiddenView} = 1;
        $ctrl->{ControllerParams::NoDoViewResponse} = 1;
        foreach($views as $view){
            $file = $viewDir."/".$view.".phtml";
            // lint php
            $g = `php -l $file`;
            echo "lint : ".$g;
            try{
                $ctrl->setCurrentView($view, true);
            }catch(\Exception $ex){
                Logger::danger(sprintf("something bad happend for %s - %s", $view, $ex->getMessage()));
            }
        }
    }
}