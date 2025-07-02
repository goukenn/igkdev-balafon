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
class SearchFilesCommand extends AppExecCommand{
	var $command='--find';
	var $desc='find file with regex pattern'; 
	var $options=[
		'--real-only'=>'flag: real file only '
	]; 
	var $category = 'sys';
	var $usage = 'directory pattern [options]';
	public function exec($command, ?string $dir=null, ?string $pattern=null){ 
		$dir ?? igk_die('missing directory');
		$T = 0;
		$real = property_exists($command->options, '--real-only');
		$pattern = $pattern ? '/'.$pattern.'/': '/.*/';
		$ls = IO::GetFiles($dir, function($f) use($pattern, & $T, $real){
			if (!$real || (realpath($f)==$f)){
			if (preg_match($pattern, $f)){
				Logger::print($f);
				$T ++;
			}
		}
		}, true);

		// if ($ls){
		// sort($ls);
		// array_map(function($q){
		// 	Logger::print($q);
		// }, $ls);
	// }

		Logger::info('total: '. $T); // count($ls));
	}
}