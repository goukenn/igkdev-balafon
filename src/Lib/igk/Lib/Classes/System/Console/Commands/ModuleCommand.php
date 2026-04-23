<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ModuleListCommand.php
// @date: 20220803 13:48:57
// @desc: module base command
namespace IGK\System\Console\Commands;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Console\App;
use IGK\System\Console\Colorize;
use IGK\System\Exceptions\NotImplementException;
use igk\System\Console\Commands\Utility;
use function igk_resources_gets as __;

/**
 * module base command
 * @package IGK\System\Console\Commands
 */
class ModuleCommand extends AppExecCommand{
    /**
    * Property: command.
    * @var mixed
    */
    var $command = "--module";
    /**
    * Property: category.
    * @var mixed
    */
    var $category = "module";
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc  = "module management command";
    /**
    * Property: options.
    * @var mixed
    */
    var $options = [];
    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = "action [options]";
    /**
    * Shows Usage.
    */
    protected function showUsage()
    {
        parent::showUsage();
        $v_actions = [
            'ls'=>'list all installed module',
            'install'=>'install a module from a repos',
            'remove'=>'remove installed module',
            'check'=>'check installed module',
        ];
        Logger::print('');
        Logger::print('action*');
        Logger::print('');
        Utility::PrintCommand($v_actions);
    }
    /**
    * Exec.
    * @param mixed $command
    * @param mixed $args
    */
    public function exec($command, $args="ls"){
       $args = $args ?? "ls";
       switch($args){
            case 'ls':
                $this->listCommand();
            break;
           default:
                if (method_exists($this, $fc = '_'.$args.'Command')){
                    $targ = array_slice(func_get_args(), 2);
                    array_unshift($targ, $command);
                    call_user_func_array([$this, $fc], $targ); 
                } else{
                     $this->listCommand();
                }
           break;
       }
    }
    /**
    * auto generate doc.
    * @param mixed $command
    * @param null|string $module
    * @return
    */
    private function _checkCommand($command, ?string $module=null){
        $mod = igk_get_module($module) ?? igk_die('module not found');
        $conf = $mod->getConfigs();
        $ls_mod = igk_get_modules();
        $info = (object)[
            'declaredDir'=>$mod->getDeclaredDir(),
            'name'=>$mod->getName(),
            'version'=>$mod->version,
            'author'=>$mod->author,
            'description'=>$mod->description
        ];
        Logger::SetColorizer(new Colorize());
        igk_wln_e('module found:', $mod->getDeclaredDir(), json_encode($info, JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES));
    }
    /**
    * auto generate doc.
    * @return
    */
    private function listCommand(){
        $mod = igk_get_modules();
        if (!$mod  || (count($mod) == 0)){
            Logger::info(__("No module installed at ".igk_get_module_dir())); 
            return;
        }
        foreach($mod as $k=>$v){
            $a = is_object($v->author) ? igk_getv($v->author, 'name' ): $v->author;
            $tag = "\r\t\t";
            $f = $k;
            $f .= "\n".$tag.$a;
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
    /**
    * auto generate doc.
    * @return
    */
    private function _installCommand(){
        throw new NotImplementException(__METHOD__);
    }
    /**
    * auto generate doc.
    * @return
    */
    private function _removeCommand(){
        throw new NotImplementException(__METHOD__);
    }
}