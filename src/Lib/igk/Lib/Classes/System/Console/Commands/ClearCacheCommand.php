<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ClearCacheCommand.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Console\Commands;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;

/**
* Clear cache command.
* @package IGK\System\Console\Commands
*/
class ClearCacheCommand extends AppExecCommand{

    /**
    * Property: command.
    * @var mixed
    */
    var $command = "--clearcache";

    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = "clear cache command";

    /**
    * Property: category.
    * @var mixed
    */
    var $category = "administration";
    /**
     * exec the command
     */

    public function exec($command)
    { 
        //defined("NO")
        Logger::print("Cache directory : ".igk_io_cachedir());  
        \IGK\Helper\SysUtils::ClearCache();
        $fc = get_included_files();
        igk_io_w2file(getcwd().'/inc_files.json', json_encode($fc));
        Logger::success("done");
    }
}