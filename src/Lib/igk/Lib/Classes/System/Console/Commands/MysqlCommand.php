<?php

// @author: C.A.D. BONDJE DOUE
// @filename: MysqlCommand.php
// @date: 20220727 19:46:27
// @desc: 

namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Database\MySQL\Controllers\DbConfigController;
use function igk_resources_gets as __;

require_once IGK_LIB_DIR . "/api/.mysql.pinc";

/**
 * 
 * @package IGK\System\Console\Commands
 */
class MySQLCommand extends AppExecCommand
{
    var $command = "--db:mysql";
    var $desc = "mysql db managment command";
    var $category = "db";
    public function sendQuery($query)
    {
        if (preg_match("/^(CREATE|INSERT|ALTER)/i", $query)) {
            Logger::print($query);
        }
        if (preg_match("/^SELECT Count\(\*\) /i", $query)) {
            // force table creation query igk_wln($query);
            return null;
        }
        return true;
    }
    public function help()
    {
        Logger::success($this->command . " [controller] [--action:options*]");
        Logger::print("");
        Logger::print($this->desc);
        Logger::print("");
        Logger::info("options*:");
        foreach (explode("|", "info|initdb|resetdb|dropdb|migrate|seed|export_schema|preview_create_query|connect") as $k) {
            Logger::print("\t{$k}");
        }
    }
    public function exec($command, $ctrl = null)
    {
        DbCommandHelper::Init($command);
        $c = igk_app()->getControllerManager()->getControllers();
        $ac = igk_getv($command->options, "--action");

        switch ($ac) {
            case null:
                Logger::danger(__("no --action defined"));
                return;
            case "connect":// check connection
                if ($db = igk_get_data_adapter(IGK_MYSQL_DATAADAPTER)){
                    $db->resetDbManager();
                    if ($db->connect()){
                        Logger::success("connexion success");
                        $db->close();
                        return 1;
                    }else{
                        igk_wln($db);
                        Logger::danger("failed to connect");
                    }
                }else {
                    Logger::danger("failed get data adapter");
                }
                return false;
            case "info":

                $d = igk_array_extract(igk_configs(), "db_name|db_server|db_user|db_pwd|db_port");
                Logger::print(json_encode(
                    $d,
                    JSON_PRETTY_PRINT
                ));
                exit;
                break;
            case "export_schema":
                // export global schema
                igk_api_mysql_get_data_schema(DbConfigController::ctrl(), 1, []);
                igk_exit();
                break;
            case "initdb":
                foreach ($c as $m) {
                    if ($m->getCanInitDb() && ($m->getDataAdapterName() == IGK_MYSQL_DATAADAPTER)) {
                        Logger::info("initdb: " . get_class($m));
                        $m::register_autoload();
                        if ($m::initDb(false, true)) {
                            Logger::success("initdb: " . get_class($m));
                        }
                    }
                }
                return 1;
            case "dropdb":
                foreach ($c as $m) {
                    if ($m->getCanInitDb() && ($m->getDataAdapterName() == IGK_MYSQL_DATAADAPTER)) {
                        Logger::info("drop: " . get_class($m));
                        $m::register_autoload();
                        if ($m::dropdb(false, true)) {
                            Logger::success("drop: " . get_class($m));
                        }
                    }
                }
                return 1;
            case "migrate":
                foreach ($c as $m) {
                    if ($m->getCanInitDb() && ($m->getDataAdapterName() == IGK_MYSQL_DATAADAPTER)) {
                        Logger::info("migrate: " . get_class($m));
                        $m::register_autoload();
                        if ($m::migrate(false, true)) {
                            Logger::success("migrate: " . get_class($m));
                        }
                    }
                }
                return 1;
            case "seed":
                foreach ($c as $m) {
                    if ($m->getCanInitDb() && ($m->getDataAdapterName() == IGK_MYSQL_DATAADAPTER)) {
                        Logger::info("seed: " . get_class($m));
                        $m::register_autoload();
                        if ($m::seed(false, true)) {
                            Logger::success("seed: " . get_class($m));
                        }
                    }
                }
                return 1;
            case "preview_create_query":
                return $this->preview_create_query($ctrl, ...array_slice(func_get_args(), 2));
            case "resetdb":
                igk_environment()->mysql_query_filter = 1;
                $ad = igk_get_data_adapter(IGK_MYSQL_DATAADAPTER);
                $ad->setSendDbQueryListener($this);
                if ($ctrl && ($c = igk_getctrl($ctrl, false))) {
                    $c = [$c];
                } else {
                    $c = igk_app()->getControllerManager()->getControllers();
                }
                if ($c) {
                    ob_start();
                    foreach ($c as $m) {
                        if ($m->getCanInitDb() && ($m->getDataAdapterName() == IGK_MYSQL_DATAADAPTER)) {
                            // Logger::print("build : " . get_class($m));
                            $m::resetDb(false, true);
                            //Logger::success("complete: ".get_class($m));
                        }
                    }
                    Logger::print("");
                    echo "#Query: \n" . ob_get_clean();
                    $ad->setSendDbQueryListener(null);
                    return 1;
                }
                break;
            default: 
                Logger::danger(__("action [{0}] not found", $ac));
                break;
        }
        return -1;
    }
    private  function preview_create_query($ctrl, $table)
    {
        $ad = igk_get_data_adapter(IGK_MYSQL_DATAADAPTER);
        $ad->setSendDbQueryListener($this);
        if (!($ctrl && ($ctrl = igk_getctrl($ctrl, false)))) {
            return -1;
        }
        Logger::info("preview create query");

        igk_environment()->mysql_query_filter = 1;

        if (($ctrl->getDataAdapterName() == IGK_MYSQL_DATAADAPTER)) {
            $ad->setSendDbQueryListener($this);
            $tb = igk_db_get_table_name($table, $ctrl);
            $def = igk_db_get_table_info($tb);
            if ($def) {
                $query = $ad->getGrammar()->createTableQuery($tb, $def["ColumnInfo"], $def["Descriptions"]);
                Logger::print($query);
            }
            $ad->setSendDbQueryListener(null);
        } else {
            Logger::danger("not a create query");
        }
    }
}
