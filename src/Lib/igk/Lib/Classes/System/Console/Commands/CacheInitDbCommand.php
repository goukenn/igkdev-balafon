<?php
namespace IGK\System\Console;
use IGK\System\Caches\DBCaches;
use IGK\System\Console\AppExecCommand;
/**
 * clear db cache command 
 * @package IGK\System\Console
 */
class InitDbCacheCommand extends AppExecCommand{

    /**
    * Property: command.
    * @var mixed
    */
    var $command = '--db:clearcache';

    /**
    * Property: category.
    * @var mixed
    */
    var $category = 'db';

    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = 'clear db cache';

    /**
    * Exec.
    * @param mixed $command
    */
    public function exec($command)
    {
        Logger::print('clear - dbcache');
        DBCaches::Clear();
        Logger::success('done');
        return 0;   
    }
}