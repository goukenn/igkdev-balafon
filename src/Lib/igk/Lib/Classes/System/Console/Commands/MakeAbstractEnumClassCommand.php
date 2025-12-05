<?php
// @author: C.A.D. BONDJE DOUE
// @file: MakeAbstractEnumClassCommand.php
// @date: 20251205 12:15:01
namespace IGK\System\Console\Commands;

use IGK\Helper\StringUtility;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;
use IGK\System\IO\Path;
use IGK\System\IO\StringBuilder;
use IGK\System\Traits\EnumeratesConstants;

/**
 * 
 * @package IGK\System\Console\Commands
 * @author C.A.D. BONDJE DOUE
 */
class MakeAbstractEnumClassCommand extends AppExecCommand
{
	var $command = '--make:enum';
	var $desc = 'make abstract enum class';
	var $options=['--force'=>'force file re-creation'];
	var $category = 'make'; 
	var $usage = 'controller name value [options]';
	public function exec($command, ?string $controller = null, ?string $name = null, ?string $value = null)
	{

		$ctrl = self::GetController($controller);
		$name || igk_die('missing name');
		$value || igk_die('missing values');
		$n = StringUtility::AutoPrefix(StringUtility::FuncName($name), "Enum");
		$force = property_exists($command->options, '--force');
		$file = Path::Combine($ctrl->getClassesDir(), $n . '.php');
		if (!$force && is_file($file)){
			Logger::danger('file already exists');
			igk_exit(-1);
		}

		$g = array_filter(array_map(function ($v) {
			$t = trim($v);
			if ($t)
				return 'const ' . StringUtility::Slugify($t) . ' = \'' . $t . '\';';
		}, explode(',', $value)));

		$builder = new PHPScriptBuilder;
		$ns = $ctrl->getEntryNamespace();
		$builder->name($n)
			->namespace($ns)
			->uses([
				EnumeratesConstants::class
			])
			->type('class')
			->class_modifier('abstract')
			->defs(implode("\n", [
				"use EnumeratesConstants;",
				implode("\n", $g)
			]));

	
		Logger::info('generate: ' . $file);
		igk_io_w2file($file, $builder->render());
	}
}
