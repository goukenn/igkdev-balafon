<?php

namespace IGK\System\Console;

use IGK\System\Caches\DBCaches;
use IGK\System\Console\AppExecCommand;

class InitDbCacheCommand extends AppExecCommand{
    var $command = '--cache-initdb';


    public function exec($command)
    {
        Logger::print('init db - cache');
        DBCaches::Clear();

        return 0;   
    }
}