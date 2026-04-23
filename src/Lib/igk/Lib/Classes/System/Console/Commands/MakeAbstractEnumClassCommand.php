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
use IGK\System\Traits\EnumeratesConstants;

/**
* auto generate doc.
* @package IGK\System\Console\Commands
* @author C.A.D. BONDJE DOUE
*/
class MakeAbstractEnumClassCommand extends AppExecCommand
{
    /**
    * Property: command.
    * @var mixed
    */
    var $command = '--make:enum';
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = 'make abstract enum class';
    /**
    * Property: options.
    * @var mixed
    */
    var $options = [
		'--force' => 'force file re-creation',
		'--enum' => 'flag: enable php8 enum definition',
		'--strict' => 'flag: enable declare strict',
		'--no-save' => 'flag: do not save file'
	];
    /**
    * Property: category.
    * @var mixed
    */
    var $category = 'make';
    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = 'controller name value [options]';
    /**
    * Exec.
    * @param mixed $command
    * @param null|string $controller
    * @param null|string $name
    * @param null|string $value
    */
    public function exec($command, ?string $controller = null, ?string $name = null, ?string $value = null)
	{
		$ctrl = self::GetController($controller);
		$name || igk_die('missing name');
		$value = $value ?? '';
		$n = StringUtility::AutoPrefix(StringUtility::FuncName($name), "Enum");
		$force = property_exists($command->options, '--force');
		$enum = property_exists($command->options, '--enum');
		$strict = property_exists($command->options, '--strict');
		$no_save = property_exists($command->options, '--no-save');
		$file = Path::Combine($ctrl->getClassesDir(), $n . '.php');
		if (!$force && is_file($file)) {
			Logger::danger('file already exists');
			igk_exit(-1);
		}
		if (!$enum) {
			$g = array_filter(array_map(function ($v) {
				$t = trim($v);
				if ($t)
					return 'const ' . self::_ToIdentifier($t) . ' = \'' . $t . '\';';
			}, explode(',', $value)));
		} else {
			$g = array_filter(array_map(function ($v) {
				$t = trim($v);
				if ($t)
					return 'case ' . self::_ToIdentifier($t) . ';';
			}, explode(',', $value)));
		}
		$builder = new PHPScriptBuilder;
		$ns = $ctrl->getEntryNamespace();
		$builder->name($n)
			->namespace($ns)
			->uses([
				EnumeratesConstants::class
			])
			->strict($strict)
			->type($enum ? 'enum' : 'class')
			->class_modifier('abstract')
			->defs(implode("\n", array_filter([
				!$enum ? "use EnumeratesConstants;" : null,
				implode("\n", $g)
			])));
		$s = $builder->render();
		if ($no_save) {
			igk_wln($s);
		} else {
			Logger::info('generate: ' . $file);
			igk_io_w2file($file, $s);
		}
	}
	/**
	 * slug and identify
	 * @param mixed $t 
	 * @return string 
	 */
	private static function _ToIdentifier($t): string
	{
		$r = StringUtility::Slugify($t);
		$r = str_replace('-', '_', $r);
		return $r;
	}
}