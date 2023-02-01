<?php

namespace IGK\System\Console;

use IGK\System\Caches\DBCaches;
use IGK\System\Console\AppExecCommand;

/**
 * clear db cache command 
 * @package IGK\System\Console
 */
class InitDbCacheCommand extends AppExecCommand{
    var $command = '--db:clearcache';
    var $category = 'db';
    var $desc = 'clear db cache';
    public function exec($command)
    {
        Logger::print('clear - dbcache');
        DBCaches::Clear();
        Logger::success('done');
        return 0;   
    }
}