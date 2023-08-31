<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ModuleListCommand.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Console\App;
use igk\System\Console\Commands\Utility;

use function igk_resources_gets as __;

/**
 * module base command
 * @package IGK\System\Console\Commands
 */
class ModuleCommand extends AppExecCommand{
    var $command = "--module";
    var $category = "module";
    var $desc  = "module management command";
    var $options = [];

    var $usage = "action [options]";

    protected function showUsage()
    {
        parent::showUsage();
        $v_actions = [
            'ls'=>'list all installed module'
        ];
        Logger::print('');
        Logger::print('action*');
        Logger::print('');
        Utility::PrintCommand($v_actions);
    }
    public function exec($command, $args="ls"){
       $args = "ls";
       switch($args){
           default:
            $this->listCommand();
           break;
       }
    }
    private function listCommand(){
        $mod = igk_get_modules();
        if (!$mod  || (count($mod) == 0)){
            Logger::info(__("No module installed at ".igk_get_module_dir())); 
            return;
        }
        foreach($mod as $k=>$v){
            $tag = "\r\t\t";
            $f = $k;
            $f .= "\n".$tag.$v->author;
            $tag .= "\t\t\t";
            $f .= $tag.$v->version;
            $tag .= "\t";
            $mod = igk_get_module($k);
            if (!$mod){
                $f.= $tag.App::Gets( App::RED, "module not found");
            }else {
                $f .= $tag.$mod->getDeclaredDir(); 
            }
            Logger::print($f); 
        }
    }
}