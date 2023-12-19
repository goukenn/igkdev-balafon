<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssLibraryCommand.php
// @date: 20230509 11:00:37
namespace IGK\System\Console\Commands\CssCommands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;

///<summary></summary>
/**
* genereate css library command
* @package IGK\System\Console\Commands\CssCommands
*/
class CssLibraryCommand extends AppExecCommand{
	var $command='--css:lib-export';
	var $category = 'css';

	/* var $desc='desc'; */
	/* var $options=[]; */
	/* var $category; */
	public function exec($command, ?string $filename=null) {
		is_null($filename) && igk_die('missing filename');
		Logger::success('done');
	}
}