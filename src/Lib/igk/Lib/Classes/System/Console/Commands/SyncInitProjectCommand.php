<?php
// @author: C.A.D. BONDJE DOUE
// @file: SyncInitProjectCommand.php
// @date: 20230225 19:37:58
namespace IGK\System\Console\Commands;

use IGK\Helper\JSon;
use IGK\System\Console\Logger;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands
*/
class SyncInitProjectCommand extends SyncAppExecCommandBase{
    var $command="--sync:init-project";
    var $help = "initialize sync project - configuration file";

    var $options= [
        "--force"=>"flag: force config file creation",
        "--ignoredirs:dir"=>"directory or expression to ignore"
    ];

    public function exec($command, ?string $controller=null) { 
        if (is_null($controller)){
            igk_die("controller required");
        }
        $ctrl= self::GetController($controller, true);
        $force = property_exists($command->options, "--force");
        if (file_exists($file = $ctrl->getDeclaredDir()."/.balafon-sync.project.json")){
            if (!$force){
                igk_die(".balafon-sync.project.json config file already exists");
            }
        }
        $setting = new \IGK\Sync\ProjectSettings;
        $dir = igk_getv($command->options, '--ignoredirs');
        if (is_string($dir)){
            $dir = [$dir];
        }
        $setting->ignoredirs = $dir;
        $setting->leavedirs = ["Data/assets"];      
        igk_io_w2file($file, JSon::Encode($setting, ['ignore_empty'=>true] , JSON_PRETTY_PRINT));
        Logger::info("create : ".$file);
        Logger::success('done');
    }
    
}