<?php
// @author: C.A.D. BONDJE DOUE
// @filename: MySQLSchemaCommand.php
// @date: 20220727 19:02:51
// @desc: get stored db schema
namespace IGK\System\Console\Commands;
use IGK\System\Console\AppExecCommand;
use IGK\System\Database\MySQL\Controllers\DbConfigController;
require_once IGK_LIB_DIR."/api/.mysql.pinc";
/**
 * export mysql db to balafon db schema 
 * @package IGK\System\Console\Commands
 */
class MySQLSchemaCommand extends AppExecCommand{

    /**
    * Property: command.
    * @var mixed
    */
    var $command = "--db:mysql-schema";

    /**
    * Property: category.
    * @var mixed
    */
    var $category = "db";

    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = "get mysql stored schema";

    /**
    * Property: options.
    * @var mixed
    */
    var $options = [
        '--prefix-table'=>'flag allow prefix on view'
    ];

    /**
    * Exec.
    * @param mixed $command
    */
    public function exec($command){
        DbCommandHelper::Init($command);
        $ctrl = DbConfigController::ctrl();        
        $is_prefix_table = property_exists($command->options, "--prefix-table"); 
        igk_api_mysql_get_data_schema($ctrl, 1, [           
            "prefix-table"=>$is_prefix_table
        ]);
    }
}