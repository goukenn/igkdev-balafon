<?php
// @author: C.A.D. BONDJE DOUE
// @file: MySQLDbHelper.php
// @date: 20231017 14:17:09
namespace IGK\System\Database\MySQL\Helper;

use IGK\System\Console\Logger;
use IGKCSVDataAdapter;
use IGK\System\Database\MySQL\DataAdapter as MySQLDbAdapter;
use IGK\System\IO\StringBuilder;

///<summary></summary>
/**
 * 
 * @package IGK\System\Database\MySQL\Helper
 */
class MySQLDbHelper
{
    private static $sm_ad;
    /**
     * backup database 
     * @param IGKCSVDataAdapter $adapter 
     * @param DataAdapter $mysql 
     * @param array $tables array of object with table property
     * @param array $skip_array 
     * @return string 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */
    public static function BackupToCSV(IGKCSVDataAdapter $adapter, MySQLDbAdapter $mysql, array $tables, $skip_array = [])
    {
        $error = [];
        $warn = [];
        $out = '';
        foreach ($tables as $v) {
            $v_tbname = $v->table;
            if (in_array($v_tbname, $skip_array)) {
                continue;
            }
            Logger::info('dump: ' . $v_tbname);
            $query = $mysql->getGrammar()->createSelectQuery($v_tbname);
            $r = $mysql->sendQuery($query);
            if ($r) {
                $out .= $v_tbname . IGK_LF;
                $out .= $adapter->toCSVLineEntry($r->Columns, "name") . IGK_LF;
                if ($r->Rows) {
                    foreach ($r->Rows as $e) {
                        $out .= $adapter->toCSVLineEntry($e) . IGK_LF;
                    }
                } else {
                    $warn[] = ("notice: no data row for [" . $v_tbname . "]\r\n");
                }
                $out .= "\0" . IGK_LF;
            } else {
                $error[] = ("error: mysql adapter failed");
            }
        }
        return $out;
    }

    public static function BackupToSQL(MySQLDbAdapter $mysql, $tables)
    {
        self::$sm_ad = $mysql;
        $sb = new StringBuilder;
        foreach ($tables as $row) {
            $t = $row->table;
            Logger::info('dump : ' . $t);
            $sb->appendLine(
                implode("\n", [
                    "--",
                    sprintf("-- Table structure for table `%s`", $t),
                    "--"
                ])
            );
            $sb->appendLine(sprintf('DROP TABLE IF EXISTS `%s`;', $t));
            $sb->appendLine(<<<EOF
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
EOF);
            // CREATE TABLE QUERY

            $sb->appendLine(self::GetDatableCreateQuery($mysql, $t));  

            $sb->appendLine('/*!40101 SET character_set_client = @saved_cs_client */;');


            $sb->appendLine(
                implode("\n", [
                    "--",
                    sprintf("-- Dumping data for table `%s`", $t),
                    "--"
                ])
            );

            $sb->appendLine(sprintf('LOCK TABLES `%s` WRITE;', $t));
            $sb->appendLine(sprintf('/*!40000 ALTER TABLE `%s` DISABLE KEYS */;', $t));
            // + | generate inter query value 

            $q = new StringBuilder;
            $ch = '';
            array_map(function ($r) use ($q, &$ch) {
                $q->append(
                    $ch.
                    "(" .
                        implode(
                            ',',
                            array_map([self::class, 'DumpValue'], array_values($r->to_array()))
                        ) . ")"
                );
                $ch = ',';
            }, $mysql->select_all($t)->to_array());

            if (!$q->isEmpty()) {
                $sb->appendLine(sprintf('INSERT INTO `%s` VALUES %s', $t, $q . ';'));
            }

            // + | restore sql keys 
            $sb->appendLine(sprintf('/*!40000 ALTER TABLE `%s` ENABLE KEYS */;', $t));
            $sb->appendLine('UNLOCK TABLES;');
            $sb->appendLine();
        }

        return $sb . '';
    }
    public static function GetDatableCreateQuery($ad, string $table){
        $db_name = $ad->getDbName();
        $table_comment = null;
        $tr= $ad->sendQuery(sprintf("SELECT table_comment AS 'comment' FROM information_schema.tables ".
        "WHERE (table_schema = '%s' AND table_name='%s'".
        ")", $db_name, $table))->to_array();

        if ($tr){
            $table_comment = $tr[0]->comment ;
        }

        $options = 'ENGINE=InnoDB DEFAULT CHARSET=utf8';
        $q = sprintf("CREATE TABLE IF NOT EXISTS `%s`(", $table);

        // $g = $ad->sendQuery(sprintf("DESCRIBE `%s` FULL", $table))->to_array();
        $g = $ad->sendQuery(sprintf("SHOW FULL COLUMNS FROM `%s`", $table))->to_array();

        $fields = [];
        $info = [];
        $primary = [];
        foreach($g as $r){
            Logger::info(json_encode($r->to_array()));

            $n = $r->Field;
            $key = $r->Key;
            $t = $r->Type ?? 'Int(9)';
            $extra=  $r->Extra;
            $def = $t;
            $is_unique = $key ? preg_match('/UNI/', $key) : false;
            $is_primary = $key ? preg_match('/PRI/', $key) : false;
            $is_multi = $key ? preg_match('/MUL/', $key) : false;
            $is_default_gen = $key ? preg_match('/DEFAULT_GENERATED/', $extra) : false;
            if ($r->Null!='YES'){
                $def.=" NOT NULL";
            }
            if ($extra == 'auto_increment'){
                $def.=' AUTO_INCREMENT';
            }
            if ($r->Default){
                $def.=sprintf(" DEFAULT %s", self::DumpValue($r->Default));
            }
            if ($key){
                if ($key=="PRI"){
                    $info[] = sprintf('KEY `%s`(`%s`)', $n,$n);
                }
            }
            if ($comment = $r->Comment){
                $def .= sprintf(" Comment '%s'", $ad->escape_string($comment));
            }
            $s = sprintf('`%s` %s', $n, $def);
            $fields[]= $s;
            if ($is_primary){
                $primary[] = $n;
            }
        }
        $q.= "\n".implode(",\n", $fields);

        if (!empty($primary)){
            $q.= ",\n".sprintf('PRIMARY KEY(`%s`)',implode("`,`", $primary));
        }
        if (!empty($info)){
            $q.=",".implode(',',$info);
        }
        $q.=")";
        if ($options)
            $q.=$options;
        if ($table_comment){
            $q.="\n"."Comment '".$ad->escape_string($table_comment)."'";
        }
        $q.=";";

        Logger::info($q);

        return $q;
    }
    public static function DumpValue($v)
    {
        $ad = self::$sm_ad;
        if (is_null($v)) {
            return 'NULL';
        }
        if (is_numeric($v) || in_array($v, ['CURRENT_TIMESTAMP'])) {
            return $v;
        } 
        if ($ad){
            $v = $ad->escape_string($v);
        }
        $v = addslashes($v);
        $v = str_replace("\n",'\n',$v);
        $v = str_replace("\t",'\t',$v);
        $v = str_replace("\r",'\r',$v);
        return igk_str_surround($v, "'");
    }

    public static function DumpInsertTable($rows):string{
        $q = new StringBuilder;
        $ch = '';
        array_map(function ($r) use ($q, &$ch) {
            $q->append(
                $ch.
                "(" .
                    implode(
                        ',',
                        array_map([self::class, 'DumpValue'], array_values($r->to_array()))
                    ) . ")"
            );
            $ch = ',';
        }, $rows);
        return $q.'';
    }
}
