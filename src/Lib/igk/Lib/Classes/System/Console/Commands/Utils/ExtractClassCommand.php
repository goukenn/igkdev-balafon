<?php
// @author: C.A.D. BONDJE DOUE
// @file: ExtractClassCommand.php
// @date: 20250314 12:17:28
namespace IGK\System\Console\Commands\Utils;

use IGK\System\Console\AppExecCommand;
use IGK\System\IO\File\PHPScriptBuilderUtility;

///<summary></summary>
/**
 * 
 * @package IGK\System\Console\Commands\Utils
 * @author C.A.D. BONDJE DOUE
 */
class ExtractClassCommand extends AppExecCommand
{
	var $command = '--extract-class';
	var $desc = 'extract class from json definition';
	var $options = ['-n'=>'name of the class'];
	var $category = 'utils';
	var $usage = 'file|json_data [path] [options]';
	public function exec($command, ?string $file = null, $path = null)
	{
		if (is_null($file)) {
			igk_die('required file');
		}
		$data = json_decode(file_exists($file) ? file_get_contents($file) : $file);
		if ($data && $path){
			$data = igk_conf_get($data, $path);
		}

		if ($data){
			$name = igk_getv($command->options, '-n');
			echo PHPScriptBuilderUtility::ExtractClassDefinition($data, $name), PHP_EOL;
		} else 
		return -1;
	}
}
