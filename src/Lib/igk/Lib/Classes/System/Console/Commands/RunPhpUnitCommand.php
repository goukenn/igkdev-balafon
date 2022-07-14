<?php

namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;

class RunPhpUnitCommand extends AppExecCommand{

    var $command = "--run:phpunit";

    var $desc = "run php unit";

    var $category = "phpunit";

    public function exec($command) {

        $pwd = igk_getv($_SERVER, 'PWD', getcwd());

        igk_wln_e(
            $command->app->getConfigs(),
            $pwd, igk_configs()->db_name);
        Logger::info(implode("", ["cwd : ".$pwd ,
        " db_name:".igk_configs()->db_name,
        " server: ".igk_configs()->db_server]));

    }
}
