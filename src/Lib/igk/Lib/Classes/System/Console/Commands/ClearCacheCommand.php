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
* auto generate doc.
* @package IGK\System\Console\Commands
*/
class ClearCacheCommand extends AppExecCommand{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command = "--clearcache";

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc = "clear cache command";

    /**
    * auto generate doc.
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
        Logger::success("done");
    }
}