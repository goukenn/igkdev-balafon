<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssLibraryCommand.php
// @date: 20230509 11:00:37
namespace IGK\System\Console\Commands\CssCommands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Html\Css\CssMedia;
use IGK\System\Html\Css\CssParser;
use IGK\System\Html\Css\CssUtils;

///<summary></summary>
/**
* genereate css library
* @package IGK\System\Console\Commands\CssCommands
*/
class CssLibraryCommand extends AppExecCommand{
	var $command='--css:lib-export';
	var $category = 'css';
	var $desc = 'help export css class selection ';	
	var $options=[];
	var $usage = 'filename [options]';

	/**
	 * 
	 */
	public function exec($command, ?string $filename=null) {
		is_null($filename) && igk_die('missing filename');
		if (!file_exists($filename)){
			igk_die('missing file');
		}
		$tkeys = CssUtils::GetCssSelectorKeys(file_get_contents($filename));		
		igk_wln(json_encode($tkeys, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
		Logger::success('done');
	}
}