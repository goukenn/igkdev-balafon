<?php

// @author: C.A.D. BONDJE DOUE
// @filename: MysqlCommand.php
// @date: 20220727 19:46:27
// @desc: 

namespace IGK\System\Console\Commands;

use Error;
use IGK\Controllers\SysDbController;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Database\MySQL\Controllers\DbConfigController;
use IGK\System\Database\MySQL\DataAdapter;
use IGK\System\Database\MySQL\Helper\MySQLDbHelper;
use IGKCSVDataAdapter;
use IGKModuleListMigration;
use ZipArchive;
use IGKException;
use IGK\System\Exceptions\EnvironmentArrayException;
use Symfony\Component\Translation\Loader\CsvFileLoader;

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
    const ACTIONS = 'clean-tables|drop-tables|info|dump|restore-dump|initdb|resetdb|dropdb|migrate|seed|export_schema|preview_create_query|connect|supported-types';
    
    var $action_helps = [
        "dump"=>"--zip,--type:(sql|csv),--filter:expression"
    ];
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
        $options = explode("|", self::ACTIONS);
        sort($options);
        foreach ($options as $k) {
            Logger::print("\t{$k}");
            $help = igk_getv($this->action_helps, $k);
            if ($help){
                Logger::print(str_repeat("\t", 6).$help);
            }
        }
    }
    public function exec($command, $ctrl = null)
    {
        DbCommandHelper::Init($command);
        $c = igk_app()->getControllerManager()->getControllers();
        $ac = igk_getv($command->options, "--action");
        /**
         * var IDadaDbAdapter $db 
         */
        $db = null;
        $db = igk_get_data_adapter(IGK_MYSQL_DATAADAPTER);
        if ($db instanceof DataAdapter) {
            switch ($ac) {
                case null:
                    Logger::danger(__("no --action defined"));
                    return;
                case 'dump':
                    Logger::info('dump mysql database to csv output');
                    $zip = igk_getv($command->options, '--zip');
                    $type = igk_getv($command->options, '--type','csv');
                    $filter = igk_getv($command->options, "--filter");
                    if (is_array($filter)){
                        igk_die("--filter as array is not allowed");
                    }
                    if ($zip) {
                        Logger::warn('zip output');
                    }
                    return $this->dump_database($db, $zip, $type, $filter);
                case 'clean-tables':
                    Logger::info('clean all tables');       
                    $filter = igk_getv($command->options, "--filter");
                    if (is_array($filter)){
                        igk_die("--filter as array is not allowed");
                    }
                    return $this->clean_tables($db, $filter); 
                case "drop-tables":
                    Logger::info('drop mysql tables');
                    $filter = igk_getv($command->options, "--filter");
                    if (is_array($filter)){
                        igk_die("--filter as array is not allowed");
                    }
                    return $this->drop_tables($db, $filter);
                case 'restore-dump':
                    Logger::info('restore mysql dump');
                    $file = igk_getv($command->options, '--file');
                    if (empty($file) || !file_exists($file)) {
                        Logger::danger('missing dump file');
                        return -201;
                    }
                    return $this->restore_dump_database($db, $file);
                
                    break;
                case 'supported-types':
                    $type = $db::GetSupportedType();
                    igk_wln_e($type);
                    break;
                case "connect": // check connection 
                    $db->resetDbManager();
                    if ($db->connect()) {
                        Logger::success("connexion success");
                        $db->close();
                        return 1;
                    } else {
                        igk_wln($db);
                        Logger::danger("failed to connect");
                    }
                    return false;
                case "info":
                    $d = igk_array_extract(igk_configs(), "db_name|db_server|db_user|db_pwd|db_port");
                    Logger::print(json_encode(
                        $d,
                        JSON_PRETTY_PRINT
                    ));
                    igk_exit();
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
                    $sysdb = SysDbController::ctrl();
                    if ($index = array_search(DbConfigController::ctrl(), $c)) {
                        unset($c[$index]);
                    }
                    if ($index = array_search($sysdb, $c)) {
                        unset($c[$index]);
                    }
                    array_push($c, $sysdb);
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
                    if (!$c) {
                        $c = [];
                    }
                    array_unshift($c, SysDbController::ctrl());
                    foreach ($c as $m) {
                        if ($m->getCanInitDb() && ($m->getDataAdapterName() == IGK_MYSQL_DATAADAPTER)) {
                            // igk_wln('migrate : migrate: ' . get_class($m));
                            Logger::info("migrate: " . get_class($m));
                            $m::register_autoload();
                            if ($m::migrate(false, true)) {
                                Logger::success("migrate: " . get_class($m));
                            }
                        }
                    }
                    IGKModuleListMigration::Migrate();

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
                    $db->setSendDbQueryListener($this);
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
                        $db->setSendDbQueryListener(null);
                        return 1;
                    }
                    break;
                default:
                    Logger::danger(__("action [{0}] not found", $ac));
                    break;
            }
        }
        return -1;
    }
    private  function preview_create_query($ctrl, $table)
    {
        $ad = igk_get_data_adapter(IGK_MYSQL_DATAADAPTER);
        if ($ad instanceof DataAdapter) {


            $ad->setSendDbQueryListener($this);
            if (!($ctrl && ($ctrl = igk_getctrl($ctrl, false)))) {
                return -1;
            }
            Logger::info("# preview create query");
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
    private function dump_database(DataAdapter $ad, $zip = false, string $type='csv', ?string $filter=null)
    {
        if (!in_array($type,['csv','sql'])){
            Logger::danger(__("%s not a supported dump type"));
            return -1;
        }
        //private function all in lowercase and snake_case 
        $dump = '';
        if (!$ad->connect()) {
            Logger::danger("can't connect to database");
            return -1;
        }
        $q =  $this->select_show_tables_query($ad, $filter);
        $tables = array_map(function ($g) {
            return (object)['table' => $g->firstValue()];
        }, $ad->sendQuery($q)->to_array());

        $adapter = igk_get_data_adapter(IGK_CSV_DATAADAPTER);
        switch($type){
            case 'sql':
                $dump = MySQLDbHelper::BackupToSQL($ad, $tables, []);
                break;
            case 'csv':
            default:
            if ($adapter instanceof IGKCSVDataAdapter) {
                $dump = MySQLDbHelper::BackupToCSV($adapter, $ad, $tables, []);
            }
            break;

        }
            $ad->close();

        if ($zip) {
            $file = 'db.dump.' . date('ymd-His') . '.csv';
            igk_zip_content($file, $file, $dump, true);
            $dump = file_get_contents($file);
            unlink($file);
        }
        echo $dump;
    }

    private function UnZipRestoreDump(string $zipfile, $ad, callable $callback)
    {
        $zip = new ZipArchive;
        $c = null;
        $table = null;
        $entry = null;
        $line = null;
        if ($zip->open($zipfile, ZipArchive::RDONLY) === true) {
            $c = $zip->numFiles;
            for ($i = 0; $i < $c; $i++) {
                $n = $zip->getNameIndex($i);
                if (igk_io_path_ext(strtolower($n), '.csv')) {
                    if ($stream = $zip->getStream($n)) {
                        $buffer_length = 4096;
                        $buffer = '';
                        $offset = 0;
                        while (($buff = stream_get_contents($stream, $buffer_length, $offset)) !== false) {

                            $ln = strlen($buff);
                            if ($ln === 0) {
                                break;
                            }
                            $buffer .= $buff;
                            $callback($ad, $buffer, $table, $line, $ln < $buffer_length);
                            $offset += $ln;
                        }
                    } else {
                        Logger::danger($zip->getStatusString());
                    }
                    break;
                }
            }
            $zip->close();
        }
    }
    /**
     * restore dump csv table 
     * @param DataAdapter $ad 
     * @param string $file 
     * @return int|false|void 
     * @throws IGKException 
     * @throws Error 
     * @throws EnvironmentArrayException 
     */
    private function restore_dump_database(DataAdapter $ad, string $file)
    { 
        $table = null;
        $tline = null;
        $size = filesize($file);
        if ($size <= 0) {
            return -301;
        }
        if (!$ad->connect()) {
            return false;
        }
        $mode = 0;
        $restoreData  = function ($ad, string &$buffer, &$table, &$tline, $end = false) use (&$mode) {
            if (empty($buffer)) {
                return;
            }
            $lines = explode("\n", $buffer);
            $buffer = '';
            while (count($lines) > 0) {
                $line = array_shift($lines);
                if (empty($lines) && !$end) {
                    $buffer = $line;
                    return;
                }

                if ($line == "\0") {
                    $table = $tline = null;
                    $mode = 0;
                    continue;
                }
                if ($mode == 0) {
                    $tr = IGKCSVDataAdapter::LoadString($line);
                    if (empty($tr)){
                        Logger::danger("empty table detected");
                    }else{
                        $table = igk_getv($tr[0], 0);
                        $mode = 1;
                        Logger::info("read: " . $table);
                    }
                } else if ($mode == 1) {

                    $g = IGKCSVDataAdapter::LoadString($line)[0];
                    $tline = array_fill_keys($g, null);
                    $mode = 2;
                } else {
                    // readlin
                    Logger::info("read: load entry" . $table);
                    $entries = IGKCSVDataAdapter::LoadString($line)[0];
                    $tcount = count($tline);
                    while(count($entries)<count($tline)){
                        $entries[] = null;
                    }
                    // validate numeric data
                    $entries = array_map(function($c){ 
                        if (is_numeric($c)){
                            return floatval($c);
                        }
                        return $c;
                    }, array_slice($entries, 0, $tcount));

                    $entry = array_combine(array_keys($tline), $entries); //array_slice($entries, 0, $tcount));
                    try{
                        $this->update_data($ad, $table, $entry, 1 );
                        Logger::success('update: '.$table);
                    }catch(\Exception $ex){
                        if (strstr($ex->getMessage(), 'Incorrect integer value:')){
                            igk_wln("faile for integer value");
                        }
                        Logger::danger($ex->getMessage());
                    }
                }
            }
        };

        $r = true;
        $ad->beginTransaction();
        $ad->stopRelationChecking();

        if ($h = fopen($file, 'r')) { 
            $ATT = fread($h, 4); 
            $LENGHT = 4096;
            if ($ATT == "\x50\x4b\x03\x04") {
                fclose($h);
                self::UnZipRestoreDump($file, $ad, $restoreData);
            } else {
                $buffer = $ATT;
                while (!feof($h)) {
                    $g = fread($h, $LENGHT);
                    $buffer .= $g;
                    $restoreData($ad, $buffer, $table, $tline, strlen($g) < $LENGHT);
                }
                fclose($h);
            }
            $ad->restoreRelationChecking();
            if ($r) {
                $ad->commit();
            } else {
                $ad->rollback();
            }
            $ad->close();
        }

    }
    private static function update_data($ad, $table, $entry, $mode){
        $ad->insert($table, $entry);
    }

    private function select_show_tables_query($ad, ?string $filter=null){
        $q = "show tables";
        if ($filter){
            $q.= sprintf(" Like '%s'", $ad->escape_string($filter));
        } 
        $q.=";";
        return $q;
    }
    /**
     * clean tables 
     * @param mixed $ad 
     * @param string|null $filter filter expression
     * @return int 
     */
    private function clean_tables($ad, ?string $filter = null){
        if (!$ad->connect()){
            return -401;
        }
        $ad->stopRelationChecking();
        $q = $this->select_show_tables_query($ad, $filter);
        $tables = array_map(function ($g) {
            return (object)['table' => $g->firstValue()];
        }, $ad->sendQuery($q)->to_array());
        foreach($tables as $t){
            Logger::info("clean : ".$t->table);
            $ad->sendQuery('DELETE FROM `'.$t->table.'`');
        }
        $ad->restoreRelationChecking();

        $ad->close();
        Logger::success('done');
        return 0;
    }

    /**
     * drop tables
     * @param mixed $ad 
     * @param null|string $filter 
     * @return int|void 
     */
    private  function drop_tables($ad, ?string $filter=null){
        if (!$ad->connect()){
            return -401;
        }
        $ad->stopRelationChecking();
        $q = $this->select_show_tables_query($ad, $filter);
        $tables = array_map(function ($g) {
            return (object)['table' => $g->firstValue()];
        }, $ad->sendQuery($q)->to_array());
        foreach($tables as $t){
            Logger::info("drop : ".$t->table);
            $ad->sendQuery('DROP TABLE `'.$t->table.'`');
        }
        $ad->restoreRelationChecking(); 
        $ad->close();
        Logger::success('done');
    }
}
