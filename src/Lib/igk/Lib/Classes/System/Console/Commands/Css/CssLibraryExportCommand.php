<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssLibraryExportCommand.php
// @date: 20230509 11:00:37
namespace IGK\System\Console\Commands\CssCommands;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Html\Css\CssMedia;
use IGK\System\Html\Css\CssParser;
use IGK\System\Html\Css\CssUtils;
/**
* genereate css library
* @package IGK\System\Console\Commands\CssCommands
*/
class CssLibraryExportCommand extends AppExecCommand{

    /**
    * Property: command.
    * @var mixed
    */
    var $command='--css:lib-export';

    /**
    * Property: category.
    * @var mixed
    */
    var $category = 'css';

    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = 'help export css class selection from file';

    /**
    * Property: options.
    * @var mixed
    */
    var $options=[];

    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = 'filename [options]';

    /**
    * auto generate doc.
    */

    public function exec($command, ?string $filename=null) {
		is_null($filename) && igk_die('missing filename');
		if (!igk_io_file_exists($filename)){
			igk_die('missing file');
		}
		$tkeys = CssUtils::GetCssSelectorKeys(file_get_contents($filename));		
		igk_wln(json_encode($tkeys, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
		Logger::success('done');
	}
}