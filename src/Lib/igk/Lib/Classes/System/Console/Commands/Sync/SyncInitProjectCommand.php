<?php
// @author: C.A.D. BONDJE DOUE
// @file: SyncInitProjectCommand.php
// @date: 20230225 19:37:58
namespace IGK\System\Console\Commands\Sync;

use IGK\Helper\JSon;
use IGK\System\Console\Logger;
use IGK\System\IO\Path;
use IGKEvents;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands
*/
class SyncInitProjectCommand extends SyncAppExecCommandBase{
    
    const CONF_FILE = '.balafon-sync.project.json';
    
    var $command="--sync:init-project";
    var $desc = "initialize sync project - configuration file";
    var $options= [
        "--force"=>"flag: force config file creation",
        "--ignoredirs:dir"=>"directory or expression to ignore"
    ];
    var $usage = 'controller [options]';
    /**
     * init command - by register event 
     * @return void 
     */
    public static function InitCommand(){
        igk_reg_hook(IGKEvents::HOOK_COMMAND, function($e){
            extract($e->args);
            if ($cmd->command == '--make:project'){
                $setting = new \IGK\Sync\ProjectSettings;
                igk_io_w2file(Path::Combine($dir, self::CONF_FILE), JSon::Encode(
                $setting));
            }  
        });
    }

 

    public function exec($command, ?string $controller=null) { 
        if (is_null($controller)){
            igk_die("controller required");
        }
        $cnf = self::CONF_FILE;
        $ctrl= self::GetController($controller, true);
        $force = property_exists($command->options, "--force");
        if (file_exists($file = $ctrl->getDeclaredDir()."/". $cnf)){
            if (!$force){
                igk_die( sprintf(__("%s config file already exists"), $cnf));
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
