<?php
// @author: C.A.D. BONDJE DOUE
// @file: SearchFilesCommand.php
// @date: 20250604 15:27:20
namespace IGK\System\Console\Commands;

use IGK\Helper\IO;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;

/**
 * 
 * @package IGK\System\Console\Commands
 * @author C.A.D. BONDJE DOUE
 */

/**
* auto generate doc.
* @package IGK\System\Console\Commands
*/
class SearchFilesCommand extends AppExecCommand
{

    /**
    * Property: command.
    * @var mixed
    */
    var $command = '--find';

    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = 'find file with regex pattern';

    /**
    * Property: options.
    * @var mixed
    */
    var $options = [
		'--real-only' => 'flag: real file only '
	];

    /**
    * Property: category.
    * @var mixed
    */
    var $category = 'sys';

    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = 'directory pattern [options]';

    /**
    * auto generate doc.
    * @param null|string $pattern
    * @return void
    */

    public function exec($command, ?string $dir = null, ?string $pattern = null)
	{
		$dir ?? igk_die('missing directory');
		$T = 0;
		$real = property_exists($command->options, '--real-only');
		$pattern = $pattern ? '/' . $pattern . '/' : '/.*/';
		//if (is_link($dir)){
		$dirs = [];
		if ($dir = realpath($dir)) {
			//}
			 IO::GetFiles($dir, function ($f) use ($pattern, &$T, $real, $dirs) {
				$p = realpath($f);
				if (!$real || ($p == $f)) {
					$c_dir = dirname($p);
					if (preg_match($pattern, $f)) {
						Logger::print($f);
						$T++;
					}
					$dirs[$c_dir] = 1;
				}
			}, true);
		}
		 
		Logger::info('total: ' . $T); // count($ls));
	}
}
