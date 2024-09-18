<?php
// @author: C.A.D. BONDJE DOUE
// @file: ModuleMakeClassCommand.php
// @date: 20230103 22:47:37
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Commands\Traits\ClassBuilderTrait;
use IGK\System\Console\Logger;
use IGK\Tests\Controllers\ModuleBaseTestCase;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands
*/
class ModuleMakeClassCommand extends AppExecCommand{
    use ClassBuilderTrait;
    var $command = '--module:make-class';
    var $category = 'module';
    var $desc = 'help create a class|interface|trait for module';
    var $options = [
        '--type:[type]'=>'class type. class|interface|trait',
        "--desc:[text]" => "description of the class",
        "--test" => "create a test file"
    ];
    public function showUsage(){
        $this->showCommandUsage(" module [class_path] [options]");
    }
    public function exec($command, ?string $module = null, ?string $class_path=null) 
    {

        if (!$module || !($mod = igk_get_module($module))){
            Logger::danger('module not found');
            return -1;
        }
        if (igk_is_null_or_empty($class_path)){
            Logger::danger('class path require');
            return -1;
        }
        $type = igk_getv($command->options, "--type", "class");
        $desc = igk_getv($command->options, "--desc");
        $force = property_exists($command->options, "--force");
        $test = property_exists($command->options, "--test");
         if (!in_array($type, self::GetAllowedTypes())) {
            $type = "class";
        }
        $ns = $mod->getEntryNamespace();        
        $no_test_class = false;

        if (!$test && (strpos($class_path,'./') === 0)){
            $class_path = substr($class_path, 2);
            $b = explode("/", $class_path, 2);
            if ($b[0]=="Tests"){
                $test = true;
                $no_test_class=true;
                $class_path = $b[1];
            }
        }
        $dir = ($test ? $mod->getTestClassesDir(): $mod->getClassesDir());  
        $extends = igk_getv($command->options, "--extends", $test && !$no_test_class ? ModuleBaseTestCase::class : null);
      

        if (!$no_test_class && $test && !igk_str_endwith($class_path, 'Test')){
            $class_path .= 'Test';
        }
        if ($test){
            $ns .= '\\Tests';
        }
        if ($f = $this->makeClass($command, $dir, $class_path, $type, $ns,$extends, $desc, $force )){
            Logger::success($f);
            return 0;
        }
        return 200;
    }

}