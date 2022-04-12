<?php

namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Database\MySQL\Controllers\DbConfigController;
require_once IGK_LIB_DIR."/api/.mysql.pinc";

class MySQLSchemaCommand extends AppExecCommand{

    var $command = "--db:mysql-schema";
    var $cat = "db";

    var $desc = "get mysql stored schema";

    public function exec($command){
        $ctrl = DbConfigController::ctrl();
        $is_prefix_table = property_exists($command->options, "--prefix-table");
        igk_api_mysql_get_data_schema($ctrl, 1, ["prefix-table"=>$is_prefix_table]);
    }


}