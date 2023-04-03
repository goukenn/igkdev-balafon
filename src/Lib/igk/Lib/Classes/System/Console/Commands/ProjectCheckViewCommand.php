<?php
 
 // @author: C.A.D. BONDJE DOUE
 // @filename: ProjectCheckViewCommand.php
 // @date: 20220830 14:52:41
 // @desc: check views 
 

namespace IGK\System\Console\Commands;

use Exception;
use IGK\Controllers\ControllerEnvParams;
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

    protected function showUsage()
    {
        Logger::info("--project:check-view ");
        DbCommandHelper::ShowUsage();
    }


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

        DbCommandHelper::Init($command);

        // $file = $ctrl->getViewFile('.forms/enterprise');
        // $r = [];
        // $file2 = $ctrl->getViewFile('default8.phtml', 1, $r); // $this->getViewFile($v, 1, $params)
        // igk_wln_e($file, $file2, "------------------");

        $views = $ctrl->getViews($withHidden, true);
        $viewDir = $ctrl->getViewDir();
        $ctrl->{ControllerEnvParams::NoActionHandle} = 1;
        $ctrl->{ControllerEnvParams::NoCompilation} = 1;
        $ctrl->{ControllerEnvParams::AllowHiddenView} = 1;
        $ctrl->{ControllerEnvParams::NoDoViewResponse} = 1;
        $t = $ctrl->getTargetNode();
        foreach($views as $view){
            $file = $viewDir."/".$view.IGK_VIEW_FILE_EXT;
            // lint php
            $g = `php -l $file`;
            Logger::info($file);
            echo "lint : ".$g.PHP_EOL;
            try{
                $ctrl->setCurrentView($view, true);
                $t->clear();
            }catch(\Exception $ex){
                Logger::danger(sprintf("something bad happend for %s - %s", $view, $ex->getMessage()));
            }
        }
    }
}