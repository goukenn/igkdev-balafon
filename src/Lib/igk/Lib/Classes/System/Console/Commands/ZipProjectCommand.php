<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ZipProjectCommand.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;  
use IGK\System\Number;

// require_once (IGK_LIB_DIR."/Lib/Classes/Resources/R.php");


class ZipProjectCommand extends AppExecCommand{

    var $command = "--zipproject";

    var $desc = "zip balafon project core";


    public function exec($command, $controller=null, $path=null){
       
        if (!extension_loaded("zip") && !function_exists('zip_open')){
            Logger::danger("zip utility function not found");
            return -1;
        }

        $ctrl = igk_getctrl(str_replace("/", "\\", $controller), false);
        if (!$ctrl){
            Logger::danger("controller $controller not found");
            return false;
        }

        $prjname = igk_str_snake(basename(igk_dir(get_class($ctrl))));
        $fname = "/project_.".$prjname.".".date("Ymd").".zip";
        if ($path == null){
            $path = getcwd().$fname;
        } else if (is_dir($path)){
            $path = $path.$fname;
        }
        $author = $command->app->getConfigs()->get("author", IGK_AUTHOR);
        igk_sys_zip_project($ctrl, $path, $author );
        Logger::success("zip project: ".$path . " : ". Number::GetMemorySize(filesize($path)));
    }
      
}