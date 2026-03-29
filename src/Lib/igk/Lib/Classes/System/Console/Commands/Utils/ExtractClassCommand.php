<?php
// @author: C.A.D. BONDJE DOUE
// @file: ExtractClassCommand.php
// @date: 20250314 12:17:28
namespace IGK\System\Console\Commands\Utils;
use IGK\System\Console\AppExecCommand;
use IGK\System\IO\File\PHPScriptBuilderUtility;
/**
* auto generate doc.
* @package IGK\System\Console\Commands\Utils
* @author C.A.D. BONDJE DOUE
*/
class ExtractClassCommand extends AppExecCommand
{
    /**
    * Property: command.
    * @var mixed
    */
    var $command = '--extract-class';
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = 'extract class from json definition';
    /**
    * Property: options.
    * @var mixed
    */
    var $options = ['-n' => 'name of the class'];
    /**
    * Property: category.
    * @var mixed
    */
    var $category = 'utils';
    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = 'file|json_data [path] [options]';
    /**
    * Exec.
    * @param mixed $command
    * @param null|string $file
    * @param null|mixed $path
    */
    public function exec($command, ?string $file = null, $path = null)
	{
		if (is_null($file)) {
			igk_die('required file');
		}
		$data = json_decode(igk_io_file_exists($file) ? file_get_contents($file) : $file);
		if (is_array($data) && is_numeric($path)) {
			$data = $data[$path];
		} else {
			if ($data && $path) {
				$data = igk_conf_get($data, $path);
			}
		}
		if ($data) {
			$name = igk_getv($command->options, '-n');
			echo PHPScriptBuilderUtility::ExtractClassDefinition($data, $name), PHP_EOL;
		} else
			return -1;
	}
}