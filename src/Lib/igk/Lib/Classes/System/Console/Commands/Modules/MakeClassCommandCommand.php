<?php
// @author: C.A.D. BONDJE DOUE
// @file: MakeCommand.php
// @date: 20230702 16:16:43
namespace IGK\System\Console\Commands\Modules;

use IGK\System\Console\AppExecCommand; 
use IGK\System\Console\Commands\Traits\ClassBuilderTrait;
use IGK\System\Console\Logger;
use IGK\System\IO\Path;
use IGK\System\IO\StringBuilder;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands\Modules
*/
class MakeClassCommandCommand extends AppExecCommand{
	use ClassBuilderTrait;
	var $command='--module:make-command';
	var $desc='make module\'s command';

	/* var $options=[]; */
	/* var $category; */
	public function exec($command, ?string $module_name = null, ?string $class_name=null) { 
		$mod = igk_get_module($module_name) ?? igk_die('missing or not found module');
		empty($class_name) && igk_die('class name required');

		$clpath = Path::Combine(\System\Console\Commands::class, $class_name);
		if (!igk_str_endwith($class_name, 'Command')){
			$clpath .= 'Command';
		}
        $cl = $mod->resolveClass($clpath);
		$test = false;// property_exists($command->options, "--test");
		$desc = igk_getv($command->options, '--desc');
		$force = property_exists($command->options, '--force');
        if (is_null($cl) || $force || !class_exists($cl)){
			$cl = $clpath;
            //make command  
			$dir = ($test ? $mod->getTestClassesDir(): $mod->getClassesDir());       
			if ($f = $this->makeClass($command, $dir, $cl, 'class', $mod->getEntryNamespace(), AppExecCommand::class, $desc, 
				$force,
				implode("\n", 
				[
					'var $command="command";',
					'var $desc="description";',
					'var $category="category";',
					'// var $options=[];',
					'// var $usage=\'\';',
					'public function exec($command) { }'
				]
				)
			)){
				Logger::success($f);
				return 0;
			}
			return -200;          
        } else {
            Logger::danger('class already exists.');
        }
	}
}