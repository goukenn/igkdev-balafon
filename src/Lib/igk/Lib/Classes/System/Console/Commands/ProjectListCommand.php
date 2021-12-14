<?php
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGKNumber;
use ZipArchive;
use IGK\Resources\R;
use IGK\System\Console\App;
use IGKControllerManagerObject;

// require_once (IGK_LIB_DIR."/Lib/Classes/Resources/R.php");


class ProjectListCommand extends AppExecCommand{

    var $command = "--project:list";

    var $desc = "List installed project";

    public function exec($command, $pattern =".+") { 
   
        $c = igk_sys_get_projects_controllers(); 
        $t = [];
        foreach ($c as $m) {
            if (preg_match("#" . $pattern . "#", $cl = get_class($m))) {
                $cl = $command->app::gets(App::PURPLE, $cl)."\n\r\t\t";
                $cl.= $m->getDeclaredDir();
                $cl.="\n\r\t\t";
                $cl.= igk_io_collapse_path($m->getDeclaredDir());
                $t[] = $cl;
            }
        }
        Logger::info("Result\n");
        Logger::print(implode("\n\n", $t)."\n");
    }

    

}