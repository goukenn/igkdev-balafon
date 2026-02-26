<?php
// @author: C.A.D. BONDJE DOUE
// @filename: RunPhpUnitCommand.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Console\Commands;
use IGK\Database\DbSchemas;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;

/**
* auto generate doc.
* @package IGK\System\Console\Commands
*/
class RunPhpUnitCommand extends AppExecCommand{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $command = "--run:phpunit";

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc = "run php unit";

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category = "phpunit";

    /**
    * auto generate doc.
    * @param mixed $command
    */

    public function exec($command) {
        DbCommandHelper::Init($command);
        $pwd = igk_getv($_SERVER, 'PWD', getcwd());
        Logger::info(implode("", ["cwd : ".$pwd ,
        " db_name:".igk_configs()->db_name,
        " server: ".igk_configs()->db_server]));
    }
}