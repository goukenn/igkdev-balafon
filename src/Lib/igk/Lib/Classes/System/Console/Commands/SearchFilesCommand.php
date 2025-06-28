<?php
// @author: C.A.D. BONDJE DOUE
// @file: SearchFilesCommand.php
// @date: 20250604 15:27:20
namespace IGK\System\Console\Commands;

use IGK\Helper\IO;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands
* @author C.A.D. BONDJE DOUE
*/
class SearchFilesCommand extends AppExecCommand{
	var $command='--find';
	var $desc='find file with regex pattern'; 
	/* var $options=[]; */
	var $category = 'sys';
	var $usage = 'directory pattern [options]';
	public function exec($command, ?string $dir=null, ?string $pattern=null){ 
		$dir ?? igk_die('missing directory');

		$pattern = $pattern ? '/'.$pattern.'/': '/.*/';
		$ls = IO::GetFiles($dir, $pattern, true);
		if ($ls){
		sort($ls);
		array_map(function($q){
			Logger::print($q);
		}, $ls);
	}

		Logger::info('total: '. count($ls));
	}
}