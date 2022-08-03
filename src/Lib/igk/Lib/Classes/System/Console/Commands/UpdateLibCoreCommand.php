<?php
// @author: C.A.D. BONDJE DOUE
// @filename: UpdateLibCoreCommand.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;

class UpdateLibCoreCommand extends AppExecCommand{

    var $command = "--updatecore";

    var $desc = "update core by copy it to location";


    public function exec($command, $path=null){
       
        if (!extension_loaded("zip") && !function_exists('zip_open')){
            Logger::danger("zip utility function not found");
            return -1;
        }
        if (empty($path)){
            Logger::danger("path is empty");
            return -1;
        }
        $lib_dir = IGK_LIB_DIR;
        $ln = strlen($lib_dir);
        $ignore_dir = ["temp"];
        Logger::info($lib_dir);
        foreach(igk_io_getfiles($lib_dir) as $f ){
            if (is_link($f))
                continue;
            $p = substr($f, $ln); 
            if (preg_match("/(\/(temp|application)\/|\.(vscode|git(ignore)?|gkds|DS_Store)$)/", $p)){
                Logger::info("ignore: ".$f);
                continue;
            }
            // Logger::print("copy : ".$p);
            igk_io_w2file($path."/igk/".$p, file_get_contents($f));
        }

        Logger::success("done");
        //igk_wln_e($lib_dir);
    }
}