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
* Run php unit command.
* @package IGK\System\Console\Commands
*/
class RunPhpUnitCommand extends AppExecCommand{
    /**
    * Property: command.
    * @var mixed
    */
    var $command = "--run:phpunit";
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = "run php unit";
    /**
    * Property: category.
    * @var mixed
    */
    var $category = "phpunit";
    /**
    * Exec.
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