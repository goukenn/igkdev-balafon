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
        '--type:[type]'=>'class type . class|interface|trait',
        "--desc:[text]" => "description of the class",
    ];
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
        $extends = igk_getv($command->options, "--extends", $test ? ModuleBaseTestCase::class : null);
        if (!in_array($type, self::GetAllowedTypes())) {
            $type = "class";
        }
        $ns = $mod->getEntryNamespace();        
        $dir = ($test ? $mod->getTestClassesDir(): $mod->getClassesDir());       
        if ($f = $this->makeClass($command, $dir, $class_path, $type, $ns,$extends, $desc, $force )){
            Logger::success($f);
            return 0;
        }
        return 200;
    }

}