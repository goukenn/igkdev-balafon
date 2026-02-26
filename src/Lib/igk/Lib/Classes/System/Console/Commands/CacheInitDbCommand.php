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
    * auto generate doc.
    * @var mixed
    */
    var $command = '--db:clearcache';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category = 'db';

    /**
    * auto generate doc.
    * @var mixed
    */
    var $desc = 'clear db cache';

    /**
    * auto generate doc.
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