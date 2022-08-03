<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbCleanCommand.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\System\Console\Commands;

use Exception;
use IGK\Controllers\BaseController;
use IGK\Helper\Utility;
use IGK\System\Console\AppCommand;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\ConsoleLogger;
use IGK\System\Console\Logger;
use IGK\System\Console\App;
use IGK\System\Helper;
use IGKEvents;
use IGKNonVisibleControllerBase;
use function \igk_api_mysql_check_data_structure;

require_once IGK_LIB_DIR . "/api/igk_api.php";

/**
 * initialize data schema
 * @package IGK\System\Console\Commands
 */
class DbCleanCommand extends AppExecCommand
{
    var $command = "--db:clean";
    var $category = "db";
    var $desc = "clean database";

    public function exec($command)
    {
        if (defined('IGK_API_MYSQLPINC'))
            require_once(IGK_API_MYSQLPINC);
        DbCommandHelper::init($command);
        Logger::info("clean db");
        Logger::print("not used tables");
        $prompt  = __("do you want to delete them ? ");

        $tb = [];
        $db = igk_get_data_adapter(IGK_MYSQL_DATAADAPTER);
        //$sdb = $db->dbName;        
        //$g = $db->sendQuery("use information_schema;");
        // $g = $db->sendQuery("select DISTINCT TABLE_NAME as t1 from table_constraints WHERE TABLE_SCHEMA='".$sdb."';");        
        // $db->selectDb($sdb);
        // $checkTable = [];
        // $exists = [];
        // foreach($g->Rows as $t) {
        //     $checkTable[] = $t["t1"];
        //     if (!$db->sendQuery("SELECT count(*) FROM ".$t["t1"])->success()){
        //         $exists[] = $t["t1"];
        //     } 
        // } 
        $api = igk_getctrl(IGK_API_CTRL);
        ob_start();
        $c = igk_api_mysql_check_data_structure($api, 0, 0, function ($type, $info) use (&$tb) {
            switch ($type) {
                case "tableNotUsed":
                    $tb[] = $info;
                    break;
            }
        });
        ob_end_clean();

        if (count($tb) > 0) {
            try {
                if ((strtolower($y = readline($prompt . " (y/n) "))) == "y") {
                    $db->stopRelationChecking();
                    foreach ($tb as $table) {
                        Logger::info("drop table : " . $table);
                        $db->sendQuery("DROP Table IF EXISTS `{$table}`");
                    }
                    $db->restoreRelationChecking();
                }
            } catch (Exception $ex) {
                echo "error";
            }
        }


        Logger::success("complete");
    }
}
