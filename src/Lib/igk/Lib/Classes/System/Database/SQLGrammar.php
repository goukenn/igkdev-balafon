<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SQLGrammar.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Database;


use function igk_die as fdie;
use function igk_getv as getv;
use function igk_getev as getev;
use function igk_get_robjs as get_robjs;
use function igk_wln as _wln;
use function igk_db_get_table_info as get_db_table_info;
use function igk_db_create_opt_obj as db_create_options;
use function igk_resources_gets as __;

use function PHPUnit\Framework\isNull;

use IGK\Database\DbColumnInfo;
use IGK\Database\DbExpression;
use IGK\Database\DbLitteralExpression;
use IGK\Database\IDataDriver;
use IGK\Database\IDbColumnInfo;
use IGK\Helper\StringUtility;
use IGK\Models\ModelBase;
use IGK\System\Console\Logger;
use IGK\System\Database\MySQL\IGKMySQLQueryResult;
use IGK\System\Database\QueryBuilderConstant as queryConstant;
use IGK\System\IO\Configuration\ConfigurationReader;
use IGKException;
use IGKSysUtil;
use stdClass;

///<summary>represent sql default grammar</summary>
/**
 * represent sql default grammar. Root is mysql behaviour
 * @package IGK\System\Database
 */
class SQLGrammar implements IDbQueryGrammar
{

    /**
     * 
     * @var IDataDriver
     */
    private $m_driver;

    const FD_ID = "clId";
    const CALLBACK_OPTS = \IGK\Database\DbConstants::CALLBACK_OPTS;

    /**
     * set SQL driver to use
     * @param mixed $driver 
     * @return void 
     */
    public function setDriver($driver)
    {
        $this->m_driver = $driver;
    }
    public function __set($n, $v)
    {
        if (method_exists($this, $fc = "set" . $n)) {
            $this->$fc($v);
        }
    }

    const AVAIL_FUNC = [
        'IGK_PASSWD_ENCRYPT',
        'AES_ENCRYPT',
        'BIN', 'CHAR', 'COMPRESS', 'CURRENT_USER', 'AES_DECRYPTDATABASE',
        'DAYNAME', 'DES_DECRYPT', 'DES_ENCRYPT', 'ENCRYPT', 'HEX', 'INET6_NTOA',
        'INET_NTOA', 'LOAD_FILE', 'LOWER', 'LTRIM', 'MD5', 'MONTHNAME', 'OLD_PASSWORD',
        'PASSWORD', 'QUOTE', 'REVERSE', 'RTRIM', 'SHA1', 'SOUNDEX', 'SPACE', 'TRIM',
        'UNCOMPRESS', 'UNHEX', 'UPPER', 'USER', 'UUID', 'VERSION', 'ABS', 'ACOS', 'ASCII',
        'ASIN', 'ATAN',
        'BIT_COUNT', 'BIT_LENGTH', 'CEILING', 'CHAR_LENGTH', 'CONNECTION_ID', 'COS', 'COT',
        'CRC32', 'CURRENT_DATE', 'CURRENT_TIME', 'DATE', 'DAYOFMONTH', 'DAYOFWEEK', 'DAYOFYEAR', 'DEGREES', 'EXP',
        'FLOOR', 'FROM_DAYS', 'FROM_UNIXTIME', 'HOUR', 'INET6_ATON', 'INET_ATON', 'LAST_DAY',
        'LENGTH', 'LN', 'LOG', 'LOG10', 'LOG2', 'MICROSECOND', 'MINUTE', 'MONTH', 'NOW', 'OCT', 'ORD', 'PI',
        'QUARTER', 'RADIANS', 'RAND', 'ROUND', 'SECOND', 'SEC_TO_TIME', 'SIGN', 'SIN', 'SQRT', 'SYSDATE', 'TAN', 'TIME', 'TIMESTAMP', 'TIME_TO_SEC', 'TO_DAYS', 'TO_SECONDS',
        'UNCOMPRESSED_LENGTH', 'UNIX_TIMESTAMP', 'UTC_DATE', 'UTC_TIME', 'UTC_TIMESTAMP', 'UUID_SHORT', 'WEEK', 'WEEKDAY', 'WEEKOFYEAR', 'YEAR', 'YEARWEEK'
    ];

    /**
     * datatype that support length
     * @var string[]
     */
    protected static $LENGTHDATA = array("int" => "Int", "varchar" => "VarChar", "char" => "Char");

    /**
     * add foreign key constraint reference
     * @param string $tbname 
     * @param SchemaForeignConstraintInfo $a 
     * @return string 
     */
    public function createAddConstraintReferenceForeignQuery(string $tbname, SchemaForeignConstraintInfo $a)
    {

        if ($keyName = $a->foreignKeyName) {
            $keyName .= " ";
        }
        return sprintf('ALTER TABLE %s ADD CONSTRAINT %s', $tbname, $keyName . sprintf(
            "FOREIGN KEY (%s) REFERENCES %s (%s);",
            $a->on,
            $a->from,
            $a->columns
        ));
    }


    public function __construct(IDataDriver $driver)
    {
        ($driver === null) && die("driver must setup");
        $this->m_driver = $driver;
        // igk_wln_e("create driver .... ", $driver);
    }
    protected static function AllowedDefValue()
    {
        static $defvalue = null;
        if ($defvalue === null) {
            $defvalue = [
                "TIMESTAMP" => [
                    "CURRENT_TIMESTAMP" => 1,
                    "NOW()" => "CURRENT_TIMESTAMP",
                    "NULL" => 1
                ],
                "DATETIME" => [
                    "CURRENT_TIMESTAMP" => 1,
                    "NOW()" => 1,
                    "CURDATE" => 1,
                    "CURTIME()" => 1,
                    "NULL" => 1
                ],
                "JSON" => [
                    "{}" => "(JSON_OBJECT())",
                    "[]" => "((JSON_ARRAY())"
                ]
            ];
        }
        return $defvalue;
    }

    public function createExpression(DbExpression $expression)
    {
        if ($expression instanceof DbLitteralExpression) {
            return  sprintf(
                "%s NOT IN (%s)",
                $expression->source_model::column($expression->column_in_source_model),
                $expression->target_model::prepare()
                    ->columns(
                        [$expression->target_model->column($expression->column_in_target_model)]
                    )->get_sub_query()
            );
        }
    }
    /**
     * resolv sql type
     * @param mixed $t 
     * @return mixed 
     * @throws IGKException 
     */
    public static function ResolvType(string $t)
    {
        return getv([
            "int" => "Int",
            "uint" => "Int",
            "udouble" => "Double",
            "bigint" => "BIGINT",
            "ubigint" => "BIGINT",
            "utinyint" => "TinyINT",
            "ufloat" => "Float",
            "date" => "Date",
            "enum" => "Enum",
            "json" => "JSON",
            "datetime" => "datetime",
            "timestamp" => "timestamp",
        ], $t = strtolower($t), $t);
    }
    public static function fallbackType($t, $adapter)
    {
        switch (strtolower($t)) {
            case "json":
                if ($adapter->isTypeSupported('longtext')) {
                    return "longtext";
                }
                break;
            case "date":
                if ($adapter->isTypeSupported('datetime')) {
                    return "datetime";
                }
                break;
            case 'timestamp':
                if ($adapter->isTypeSupported('datetime')) {
                    return "TIMESTAMP";
                }
                if ($adapter->isTypeSupported('int')) {
                    return "int";
                }
                break;
            case 'char':
                return 'varchar';
        }
        return "text";
    }
    /**
     * get random row query 
     * @param string $table 
     * @param string $column 
     * @param null|array $columns 
     * @param int $limit 
     * @return string 
     */
    public function createRandomQueryTableOnColumn(string $table, string $column, ?array $columns = null, $limit = 1): ?string
    {
        $v_column = '*';
        if ($columns) {
            $v_column = self::BuildColumn($columns, $this->m_driver, false);
        }
        $query = sprintf(
            'SELECT ' . $v_column . ' FROM %s AS t1 JOIN (SELECT %s FROM %s ORDER BY RAND() LIMIT %s) as t2 ON t1.%s=t2.%s;',
            $table,
            $column,
            $table,
            $limit,
            $column,
            $column
        );
        return $query;
    }
    /**
     * check allow type length
     * @param string $type 
     * @return bool 
     */
    public function allowTypeLength(string $type): bool
    { 
        return $this->m_driver->allowTypeLength($type,null);

        
    }
    public function remove_foreign(string $table, $column): ?string
    {
        return $this->m_driver->remove_foreign($table, $column);
    }
    /**
     * create table query
     * @param mixed $tbname 
     * @param mixed $columninfo 
     * @param mixed $desc 
     * @param int $engine_name set engine name 
     * @param int $nocomment 
     * @return string 
     * @throws IGKException 
     */
    public function createTablequery(string $tablename, array $columninfo, $desc = null, $options = null)
    {
        $driver = $this->m_driver;
        $query = '';
        $query .= $this->m_driver->escape_table_name($tablename);

        $query .= "(";
        $tb = false;
        $primary = "";
        $unique = "";
        $funique = "";
        $findex = "";
        $fautoindex = "";
        $uniques = array();
        $primkey = "";
        $tinf = array();
        $defvalue = static::AllowedDefValue();
        $resovlType = igk_environment()->getResolvSQLType();
        $support = $driver->getEngineSupport();
        $engine_name = ($options ? igk_getv($options, 'Engine') : null) ??
            $support ? 0 : null;
        $nocomment = 0;

        foreach ($columninfo as $k => $v) {
            if (($v == null) || !is_object($v)) {
                fdie(__CLASS__ . " :::Error table column info is not an object error for " . $tablename);
            }

            if ($tb)
                $query .= ",";
            $v_name = $v->clName;
            if (empty($v->clName)) {
                if (is_numeric($k)) {
                    fdie(__CLASS__ . " :::Error column name must be a string");
                }
                $v_name = $k;
                $v->clName = $k;
            }
            $primkey = "noprimkey://" . $v_name;
            $v_name = $driver->escape_string($v_name);
            $query .= "" . self::GetKey($v_name,  $driver) . " ";
            $type = getev(static::ResolvType($v->clType), "Int");
            $v_fallback_type = false;
            if ($resovlType && $driver && !$driver->isTypeSupported($type)) {
                $type = static::fallbackType($type, $driver);
                $v_fallback_type = true;
            }
            $query .= $driver->escape_string($type);
            $s = strtolower($type);
            $number = $this->isNumber($s);
            if ($driver->getIsLengthData($s)) {
                if (($v->clTypeLength > 0) && $this->allowTypeLength($s)) {
                    $query .= "(" . $driver->escape_string($v->clTypeLength) . ")";
                }
            } else if ($type == "Enum") {
                $e_ev = $v->clEnumValues ?? '';
                $e_sv = 0;
                if ($e_ev) {
                    if ($g = self::GetEnumQueryValueQueryString($e_ev, $driver)){
                        $e_sv = 1;
                        $query .= '(' . $g . ')';
                    }

                }

                if (!$e_sv)
                    $query .= "(" . implode(",", array_map(function ($i) use ($driver) {
                        return "'" . $driver->escape_string($i) . "'";
                    }, array_filter(explode(",", $e_ev), function ($c) {
                        return (strlen(trim($c)) > 0);
                    }))) . ")";
            }
            $query .= " ";

            if (!empty($v->clLinkType)) {
                $driver->pushRelations($tablename, $v);
            }
            //+ | update to fallback resolution 
            if (!$v_fallback_type && static::IsUnsigned($v)) {
                $query .= "unsigned ";
            }

            if ($number) {
                if (($v->clNotNull) || ($v->clAutoIncrement))
                    $query .= "NOT NULL ";
                else
                    $query .= "NULL ";
            } else if ($v->clNotNull) {
                $query .= "NOT NULL ";
            }
            if ($v->clAutoIncrement && $driver->isAutoIncrementType($type)) {
                $query .= $driver->getParam("auto_increment_word", $v, $tinf) . " ";
                if ($idx = getv($v, "clAutoIncrementStartIndex")) {
                    $fautoindex = $driver->getParam("auto_increment_word", $v, $tinf) . "={$idx} ";
                }
            }
            $tb = true;
            if ($driver->supportDefaultValue($type) &&  (($v->clDefault) || ($v->clDefault === '0'))) {
                $_ktype = strtoupper($type);
                $_kdef = strtoupper($v->clDefault);
                $_def = $r_v = isset($defvalue[$_ktype][$_kdef]) ?
                    (is_int($defvalue[$_ktype][$_kdef]) ?
                        $v->clDefault : $defvalue[$_ktype][$_kdef]) :
                    "'" . $this->m_driver->escape_string($_kdef) . "'";
                $query .= "DEFAULT {$_def} ";

                if ($r_v && $v->clUpdateFunction) {
                    $_def = !isset($defvalue[$_ktype][$v->clUpdateFunction]) ? $v->clDefault :
                        "" . $this->m_driver->escape_string($v->clUpdateFunction) . "";

                    // + | ovh missing column on update   
                    // + | on update depend of the data type
                    // $query .= " ON UPDATE {$_def}";

                }
            }


            if ($v->clDescription && !$nocomment) {
                // description per column
                $query .= " COMMENT '" . $this->m_driver->escape_string($v->clDescription) . "' ";
            }
            // "remove end line"
            $query = rtrim($query);
            if ($v->clIsUnique) {
                if (!empty($unique))
                    $unique .= ",";
                $unique .= "UNIQUE KEY `" . $v_name . "` (`" . $v_name . "`)";
            }
            if ($v->clIsUniqueColumnMember) {
                $v_unique_columns_index = 0;
                if (!isset($v->clColumnMemberIndex)) {
                    $v_unique_columns_index = '0';
                } else {
                    $v_unique_columns_index = '' . $v->clColumnMemberIndex;
                }
                //  if ($v_unique_columns_index) {
                $tindex = explode("-", $v_unique_columns_index);
                $indexes = array();
                foreach ($tindex as $kindex) {
                    if (!is_numeric($kindex) || isset($indexes[$kindex]))
                        continue;
                    $indexes[$kindex] = 1;
                    $ck = 'unique_' . $kindex;
                    $bf = "";
                    if (!isset($uniques[$ck])) {
                        $bf .= "UNIQUE KEY `UC_" . $ck . "_index`(`" . $v_name . "`";
                    } else {
                        $bf = $uniques[$ck];
                        $bf .= ",`" . $v_name . "`";
                    }
                    $uniques[$ck] = $bf;
                }
            }
            if ($v->clIsPrimary && !isset($tinf[$primkey])) {
                if (!empty($primary))
                    $primary .= ",";
                $primary .= "" . $driver->escape_table_column($v_name) . "";
            }
            if ($v->clIsIndex || $v->clLinkType) {
                ///TODO : fix key definition 
                $v_nk = $v_name;
                if ($v->clLinkType) {
                    // + | --------------------------------------------------------------------
                    // + | add _FK_ to indicate possible Foreign key 
                    // + |                    
                    $v_nk .= '_FK';
                }

                if (!$v->clIsUniqueColumnMember  && $v->clIsPrimary) {
                }
                if (!empty($findex))
                    $findex .= ",";
                $findex .= "KEY `" . $v_nk . "_index` (`" . $v_name . "`)";
            }
            unset($tinf[$primkey]);
        }
        if (!empty($primary)) {
            $query .= ", PRIMARY KEY (" . $primary . ")";
        }
        if (!empty($unique)) {
            $query .= ", " . $unique;
        }
        if (!empty($funique)) {
            $funique .= ")";
            $query .= ", " . $funique;
        }
        if (count($uniques) > 0) {
            foreach ($uniques as $v) {
                $v .= ")";
                $query .= ", " . $v;
            }
        }
        if (!empty($findex))
            $query .= ", " . $findex;

        $query =  rtrim($query) . ")";

        if (!$engine_name) {
            $query .= ' ENGINE=InnoDB';
        } else {
            $query .= sprintf(" ENGINE=%s", $engine_name);
        }
        if (!empty($fautoindex)) {
            $query .= " " .    $fautoindex;
        }
        if ($desc) {
            $query .= " COMMENT='" . $this->m_driver->escape_string($desc) . "' ";
        }
        $query = sprintf($driver->getCreateTableFormat(["checkTable" => 1]), $query);
        // $query = rtrim($query) . ";";
        igk_ilog($query, null, 0, false);
        return $query;
    }

    /**
     * get enum value string from data definition 
     * @param string $d 
     * @return null|string 
     */
    public static function GetEnumQueryValueQueryString(string $d, $driver):?String{ 
        if ($g = ConfigurationReader::ParseEnumLitteralValue($d)) {
            // get only value
            $t = [];
            foreach($g as $k=>$v){
                $r = is_null($v) ? $k : $v;
                $t[] = "'".$driver->escape_string($r)."'";
            } 
            return implode(',', $t);
        }
    }
    /**
     * return null in case of foreign key exists in defined
     */
    public function add_foreign_key(string $table, $v, $nk = null, $db = null)
    {
        $db = $db ?? $this->m_driver->getDbName();
        if (!empty($nk) || !empty($nk = igk_getv($v, "clLinkConstraintName", ""))) {

            if ($this->m_driver->constraintForeignKeyExists($nk)) {
                $nk = null;
                return;
            } else {
                $nk = "CONSTRAINT " . $nk . " ";
            }
        }
        // $clkey =  $db ? "%s.%s" : "%s";
        $clkey = "%s(%s)";
        $tbname =   $this->joinTableName($table, $db);
        $link = $this->joinTableName($v->clLinkType, $db);

        $query = sprintf(
            $this->m_driver->createAlterTableFormat(),
            $tbname,
            $nk,
            $v->clName,
            sprintf(
                $clkey,
                $link,
                $this->m_driver->escape_table_column(
                    getv($v, "clLinkColumn", self::FD_ID)
                )
            )
        );
        return $query;
    }
    /**
     * joint table tbame
     * @param string $table 
     * @param null|string $db 
     * @param null|string $column 
     * @return string 
     */
    public function joinTableName(string $table, ?string $db = null, ?string $column = null): string
    {
        if (strpos($table, '`') !== false) {
            return $table;
        }
        $s = [];
        if ($db) {
            $s[] = sprintf('`%s`', $this->m_driver->escape_string($db));
        }
        $s[] =  sprintf('`%s`', $this->m_driver->escape_string($table));
        if ($column) {
            $s[] = sprintf('`%s`', $this->m_driver->escape_string($column));
        }
        return implode(".", $s);
    }

    /**
     * 
     * @param string $table 
     * @param string $column 
     * @return null|string|array
     */
    public function add_index(string $table, $column): ?string
    {
        if (!$column) {
            return null;
        }
        $column = $this->_get_column_list($column);
        $idx = strtolower('IDX_' . StringUtility::CamelClassName($column));
        $q = "CREATE INDEX ";
        $q .= $idx . " ON `" . $table . "` ";
        $q .= "(" . $column . ");";
        return $q;
    }
    private function _get_column_list($column)
    {

        if (!is_array($column)) {
            $column = [$column];
        }
        $column = implode(',', array_filter(array_map(function ($s) {
            return igk_str_surround($this->m_driver->escape_string($s), '`');
        }, $column)));
        return $column;
    }
    public function drop_index(string $table, $column): ?string
    {
        if (!$column) {
            return null;
        }
        $idx = null;
        if (strtolower($column) == 'primary') {
            $idx = `PRIMARY`;
        }
        $column = $this->_get_column_list($column);
        $idx = $idx ?? strtolower('IDX_' . StringUtility::CamelClassName($column));

        $q = "DROP INDEX ";
        $q .= $idx . " ON `" . $table . "`;";
        return $q;
    }

    /**
     * create add column alter query
     * @param mixed $table 
     * @param mixed $info 
     * @param mixed $after 
     * @return string 
     */
    public function add_column(string $table, $info, ?string $after = null)
    {
        Logger::warn('try add column : ' . $table . ' :-> ' . $info->clName);
        $v_clname = $this->m_driver->escape_string($info->clName);
        $v_clname = $this->m_driver->escape_string($info->clName);

        $q = "ALTER TABLE ";
        $q .= "`" . $table . "` ADD COLUMN ";
        $q .= "`" . $v_clname . "` ";

        $q .= rtrim($this->getColumnInfo($info));

        if (!empty($after)) {
            $q .= " AFTER `" . $after . "`";
        }
        $q .= ';';
        return $q;
    }
    /**
     * create alter table query 
     * @param mixed $table 
     * @param mixed $info 
     * @param mixed $after 
     * @return string 
     * @throws IGKException 
     */
    public function rm_column(string $table, $info)
    {
        $name = is_object($info) ? getv($info, "clName") : $info;
        return $this->createDropColumnQuery($table, $name);
    }
    /**
     * rename column 
     * @param mixed $table table to rename column
     * @param mixed $column old column name
     * @param mixed $new_name new column name
     * @return string|null 
     * @throws IGKException 
     */
    public function rename_column(string $table, string  $column, string $new_name): string
    {
        // + |  rename columns 
        Logger::warn("rename columns .... " . $table);
        $adapter  = $this->m_driver;
        $q = null;
        $version = $adapter->getVersion();
        if ($adapter->getType() == IGK_MYSQL_DATAADAPTER) {
            $q = "ALTER TABLE ";
            if (version_compare($version, '8.0', '>=')) {
                $q .= "`" . $table . "` RENAME COLUMN ";
                $q .= $adapter->escape($column) . " TO " . $adapter->escape($new_name) . ';';
            } else {
                // retrieve column info 
                $info = null;
                $info = $this->retrieveStoredColumnInfo($table, $column);
                if ($info) {
                    $q .= "`" . $table . "` CHANGE ";
                    $q .= $adapter->escape($column) . " " .
                        $adapter->escape($new_name) .
                        ' ' . $this->getColumnInfo($info) .
                        ';';
                }
            }
        }
        return $q;
    }

    /**
     * retrive stored info
     * @param string $table 
     * @param string $column 
     * @return null|IDbColumnInfo 
     */
    public function retrieveStoredColumnInfo(string $table, string $column): ?IDbColumnInfo
    {
        $v_info = null;
        if ($this->m_driver instanceof IDbRetrieveColumnInfoDriver) {
            $v_info = $this->m_driver->getColumnInfo($table, $column);
            if ($v_info) {
                return igk_getv(array_values($v_info), 0);
            }
        }
        return $v_info;
    }

    /**
     * 
     * @param IGK\System\Database\strign $table 
     * @param object|DbColumnInfo $info 
     * @param null|string $new_name 
     * @return ?string 
     * @throws IGKException 
     */
    public function change_column(string $table, object $info, ?string $new_name = null)
    {
        igk_debug_wln("change_column : " . $table);
        if (empty($info->clName)) {
            if (igk_environment()->isDev()) {

                igk_trace();
                igk_wln_e("empty name", $info, $table);
            }
            return null;
        }
        $column = $info->clName;
        $adapter  = $this->m_driver;
        $new_name = $adapter->escape($new_name ?? $column);
        $q = "ALTER TABLE ";
        $q .= "`" . $table . "` CHANGE ";
        $q .= $adapter->escape($column) . " " . $new_name . " " . rtrim($this->getColumnInfo($info));
        $q .= ';';
        return $q;
    }

    public function drop_foreign_key($table, $info)
    {
        // TODO: drop foreign grammar
        Logger::warn('drop foreign key');
    }
    public function drop_column($table, $column)
    {
        return $this->createDropColumnQuery($table, $column);
    }

    /**
     * is number
     * @param string $s 
     * @return bool 
     */
    public function isNumber(string $s): bool
    {
        return in_array($s, ['int', 'float']);
    }

    /**
     * get grammar column definition
     * @param mixed|IDbColumnInfo $v 
     * @param bool $nocomment 
     * @return string 
     * @throws IGKException 
     */
    public function getColumnInfo($v, bool $nocomment = false): string
    {
        $adapter  = $this->m_driver;
        $defvalue =  static::AllowedDefValue();
        $query = "";
        $tinf = null;
        $not_supported = false;

        $type = getev(static::ResolvType($v->clType), "Int");
        if (!$adapter->isTypeSupported($type)) {
            $type = static::fallbackType($type, $adapter);
            $not_supported = true;
        }
        $query .= $adapter->escape_string($type);
        $s = strtolower($type);
        $number = $this->isNumber($s);
        if (isset(static::$LENGTHDATA[$s])) {
            if (($v->clTypeLength > 0) && $this->allowTypeLength($s)) {
                $query .= "(" . $adapter->escape_string($v->clTypeLength) . ")";
            }
        } else if ($s == "enum") {
            $query .= "(" . implode(",", array_map(function ($i) {
                return "'" . $this->m_driver->escape_string($i) . "'";
            }, array_filter(explode(",", $v->clEnumValues), function ($c) {
                return (strlen(trim($c)) > 0);
            }))) . ")";
        }
        $query .= " ";

        if (!$not_supported && $v->IsUnsigned()) {
            $query .= "unsigned ";
        }

        if ($number) {
            if (($v->clNotNull) || ($v->clAutoIncrement))
                $query .= "NOT NULL ";
            else
                $query .= "NULL ";
        } else if ($v->clNotNull) {
            $query .= "NOT NULL ";
        }
        if ($v->clAutoIncrement) {
            $query .= $this->m_driver->getParam("auto_increment_word", $v, $tinf) . " ";
            if ($idx = getv($v, "clAutoIncrementStartIndex")) {
                $query .= "={$idx} ";
            }
        }
        // unit column
        if ($v->clIsUnique) {
            $query .= "UNIQUE ";
        }

        $tb = true;
        if ($v->clDefault || $v->clDefault === '0') {
            $_ktype = strtoupper($type);
            $_kdef = strtoupper($v->clDefault);
            $_def = $r_v = isset($defvalue[$_ktype][$_kdef]) ?
                (is_int($defvalue[$_ktype][$_kdef]) ?
                    $v->clDefault : $defvalue[$_ktype][$_kdef]) :
                "'" . $adapter->escape_string($_kdef) . "'";
            $query .= "DEFAULT {$_def} ";

            if ($r_v && $v->clUpdateFunction) {
                $_def = isset($defvalue[$_ktype][$v->clUpdateFunction]) ? $v->clDefault :
                    "" . $adapter->escape_string($v->clUpdateFunction) . " ";
                $query .= " ON UPDATE {$_def}";
            }
        }
        if ($v->clDescription && !$nocomment) {
            $query .= "COMMENT '" . $adapter->escape_string($v->clDescription) . "' ";
        }
        return $query;
    }

    public function createInsertQuery($tbname, $values, $tableInfo = null)
    {
        if ($tableInfo === null) {
            $tableInfo = igk_getv(igk_db_get_table_info($tbname), "ColumnInfo");
        }
        $rtbname = $this->m_driver->escape_string($tbname);
        $_tbname = $this->m_driver->escape_table_name($rtbname);
        $query = "INSERT INTO " . $_tbname . "(";
        $v_v = "";
        $v_c = 0;
        $tvalues = static::GetValues($this->m_driver, $values, $tableInfo);
        // $level = $tvalues->clLevel;
        // igk_debug_wln_e(__FILE__.":".__LINE__,  $tvalues, $values);
        foreach ($tvalues as $k => $v) {
            if ($v_c != 0) {
                $query .= ",";
                $v_v .= ",";
            } else
                $v_c = 1;
            $query .= "" . $this->m_driver->escape_table_column($k) . "";

            if ($tableInfo) {
                // | get value 
                $tinf = getv($tableInfo, $k);
                if (($v === 'NULL') && (is_null($values[$k]))) {
                    $v = null;
                }
                $v_v .= "" . static::GetValue($this->m_driver, $rtbname, $tinf, $k, $v);
            } else {
                if ($v === null) {
                    $v_v .= "NULL ";
                } else if (is_object($v) && method_exists($v, "getValue")) {
                    $v_v .= "" . $v->getValue();
                } else if (is_numeric($v)) {
                    $v_v .= $v;
                } else
                    $v_v .= "'" . $this->m_driver->escape_string($v) . "'";
            }
        }
        $query .= ") VALUES (" . $v_v . ");";
        return $query;
    }
    /**
     * 
     * @param mixed $tbname table name
     * @param mixed $values array of value to set
     * @param mixed|null $condition where condition list
     * @param mixed|null $tableInfo columns info to build the query
     * @return string 
     * @throws IGKException 
     */
    public function createUpdateQuery($tbname, $values, $condition = null, $tableInfo = null)
    {
        if (is_null($values)) {
            igk_die(__("{0} [{1}] is null", __METHOD__, "value"));
        }

        $driver = $this->m_driver;
        $rtbname = $driver->escape_string($tbname);
        $out = "";
        $t = 0;
        $v_condstr = "";
        $id = $condition == null ? getv($values, self::FD_ID) : null;
        if (($id == null) && is_integer($condition)) {
            $id = $condition;
        }
        $tableInfo = $tableInfo ?? getv(get_db_table_info($tbname), "ColumnInfo");
        $primaryKey = IGK_FD_ID;

        $tvalues = static::GetValues($this->m_driver, $values, $tableInfo, 1);
        if (empty($tvalues)) {
            return null;
        }
        foreach ($tvalues as $k => $v) {
            if ($id && ($k == self::FD_ID) || (strpos($k, ":") !== false))
                continue;
            $tinf = getv($tableInfo, $k);
            if ($t == 1)
                $out .= ",";
            if ($tableInfo) {
                $out .= "`" . $driver->escape_string($k) . "`=" . self::GetValue($this->m_driver, $rtbname, $tinf, $k, $v, "u");
            } else {
                $out .= "`" . $driver->escape_string($k) . "`=";
                if (!empty($v) && is_integer($v)) {
                    $out .= $v;
                } else
                    $out .= "'" . $driver->escape_string($v) . "'";
            }
            $t = 1;
        }
        if (!$t) {
            return null;
        }
        $out = "UPDATE `" . $rtbname . "` SET " . $out;
        if ($condition) {
            if (is_array($condition)) {
                $v_condstr .= static::GetCondString($this->m_driver, $condition);
            } else if (is_string($condition) && !preg_match(\IGK\System\Regex\RegexConstant::INT_REGEX, $condition))
                $v_condstr .= $condition;
            else if (is_integer($condition) || preg_match(\IGK\System\Regex\RegexConstant::INT_REGEX, $condition))
                $v_condstr .= "`{$primaryKey}`='" . $driver->escape_string($condition) . "'";
            else {
                _wln("data is " . $condition . " " . strlen($condition) . " ::" . is_integer((int)$condition));
            }
        } else if ($id) {
            $v_condstr .= "`{$primaryKey}`='" . $driver->escape_string($id) . "'";
        }
        if (!empty($v_condstr)) {
            $out .= " WHERE " . $v_condstr;
        }
        $out .= ";";
        return $out;
    }

    /**
     * create drop column query to send
     * @param string $tablename 
     * @param string $column_name 
     * @param null|string $dbname 
     * @return string 
     */
    public function createDropColumnQuery(string $tablename, string $column_name, ?string $dbname = null): string
    {
        $d = $this->m_driver;
        if ($dbname) {
            $tablename = sprintf("%s." . $tablename, $dbname);
        }
        return sprintf(
            "ALTER TABLE `%s` DROP COLUMN `%s`;",
            $d->escape_string($tablename),
            $d->escape_string($column_name)
        );
    }

    public static function IsUnsigned($v)
    {
        if (method_exists($v, "IsUnsigned")) {
            return $v->IsUnsigned();
        }
        return false;
    }

    ///<summary></summary>
    ///<param name="tbname"></param>
    ///<param name="tableInfo"></param>
    ///<param name="columnName"></param>
    ///<param name="value"></param>
    ///<param name="type" default="i"></param>
    /**
     * 
     * @param mixed $tbname
     * @param mixed $tableInfo
     * @param mixed $columnName
     * @param mixed $value
     * @param mixed $type the default value is "i"
     */
    public static function GetValue($driver, $tbname, IDbColumnInfo $tinf, $columnName, $value, $type = "i")
    {
        if ($tinf === null) {
            fdie("can't get column: {$columnName} info in table: {$tbname}");
        }
        $def = static::AllowedDefValue();
        if (!empty($tinf->clLinkType) && is_string($value) && (strpos($value, ".") !== false)) {
            if ($v = $driver->GetExpressQuery($value, $tinf)) {
                return $v;
            }
        }
        if (empty($value) && (($tinf->clValidator) == 'guid')) {
            if ((!$tinf->clLinkType) && ($tinf->clNotNull)) {
                // auto generate a guid if not specified
                $value = igk_create_guid();
            }
        }

        if ($value instanceof DbExpression) {
            return $value->getValue((object)[
                "grammar" => $driver,
                "type" => "insert"
            ]);
        }

        if ($value instanceof ModelBase) {
            return $value->id();
        }



        if ((is_integer($value))) {
            if (($value === 0) && !empty($tinf->clLinkType) && !$tinf->clNotNull) {
                return 'NULL';
            }
            if (($value === 0) && !empty($tinf->clLinkType) && $tinf->clNotNull) {
                // select default link expression
                if ($express = $tinf->clDefaultLinkExpression) {
                    if ($v = $driver->GetExpressQuery($express, $tinf)) {
                        return $v;
                    }
                }
            }
            if (strtolower($tinf->clType) == "enum") {
                return "'" . $driver->escape_string($value) . "'";
            }
            return $value;
        }
        if ($tinf->clType == "JSON") {
            if (is_string($value)) {
                $deco = json_decode($value);
                if (json_last_error()) {
                    igk_die("value not a valid json");
                }
                return "'" . $driver->escape_string(str_replace('\\"', '\\\\"', json_encode($deco, JSON_UNESCAPED_SLASHES))) . "'";
            }
            if ($data = json_encode($value, JSON_UNESCAPED_SLASHES)) {
                return "'" . str_replace('\\"', '\\\\"', $data) . "'";
            }
        }
        $of = 'NULL';
        if (($type == "i") && $tinf->clInsertFunction) {
            $of = $tinf->clInsertFunction;
        } else if (($type != "i") && $tinf->clUpdateFunction) {
            $of = $tinf->clUpdateFunction;
        }
        if ($of == 'IGK_PASSWD_ENCRYPT') {
            if (empty($value)) {
                $value = IGKSysUtil::Encrypt(igk_create_guid());
                $of = null;
            }
        }

        if (($value === null) || ($value === $tinf->clDefault) || (($value !== '0') && empty($value))) {
            if ($tinf->clNotNull) {
                // + allow null value

                if ($tinf->clDefault !== null) {
                    if (is_integer($tinf->clDefault)) {
                        return $tinf->clDefault;
                    } else {
                        if (static::IsAllowedDefValue($def, $tinf->clType, $tinf->clDefault)) {
                            return $tinf->clDefault;
                        }
                        return "'" . $driver->escape_string($tinf->clDefault) . "'";
                    }
                }
                // + | handle mysql fallback data
                switch (strtolower($tinf->clType)) {
                        // + | handle mysql data number
                    case 'int':
                    case 'integer':
                    case 'float':
                    case 'double':
                    case 'bigint':
                    case 'ubigint':
                    case 'smallint':
                    case 'tinyint':
                    case 'usmallint':
                    case 'utinyint':
                        if (!$tinf->clNotNull) {
                            return 'NULL';
                        }
                        return "0";
                    case "datetime":
                    case "date":
                    case "time":
                        return "NOW()";
                    case "json":
                        return "'{}'"; // empty json value                        
                    default:
                        if (is_string($value)) {
                            return "''";
                        }
                        return sprintf($of, $value);
                }
            }


            if (preg_match("/(date(time)?|timespan)/i", $tinf->clType)) {
                if (strtolower($of) == 'now()') {
                    switch (strtolower($tinf->clType)) {
                        case "datetime":
                        case "timespan":
                            return "'" . $driver->escape_string(date(\IGKConstants::MYSQL_DATETIME_FORMAT)) . "'";
                        case "date":
                            return "'" . $driver->escape_string(date(\IGKConstants::MYSQL_DATE_FORMAT)) . "'";
                        case "time":
                            return "'" . $driver->escape_string(date(\IGKConstants::MYSQL_TIME_FORMAT)) . "'";
                    }
                }
                if ($value === 'NULL') {
                    $value = null;
                }
                if ($tinf->clDefault && static::IsAllowedDefValue($def, $tinf->clType, $tinf->clDefault)) {
                    return $tinf->clDefault;
                }
            }


            if ($of != 'NULL') {
                $gt = explode("(", $of);
                $pos = strtoupper(array_shift($gt));
                if (!$tinf->clNotNull) {
                    if (in_array($pos, static::AVAIL_FUNC)) {
                        return sprintf($of, $value);
                    }
                }
            }
            if ($value && ($value == $tinf->clDefault)) {
                return "'" . $driver->escape_string($value) . "'";
            }
            return 'NULL';
        }

        if (empty($value)) {
            if (!$tinf->clNotNull || ($tinf->clAutoIncrement && strtolower($tinf->clType) == 'int'))
                return 'NULL';
        }

        if (is_object($value)) {
            if ($s = $driver->getObjValue($value)) {
                return $s;
            }
        }
        if ($tinf) {
            $of = $type == "i" ? $tinf->clInsertFunction : $tinf->clUpdateFunction;
            if (!preg_match("/date(time)?/i", $tinf->clType) && !empty($of)) {
                $gt = explode("(", $of);
                $pos = strtoupper(array_shift($gt));
                if (!empty($s = $driver->getFuncValue($pos, $value))) {
                    return $s;
                }
                return strtoupper($pos) . "('" . $driver->escape_string($value) . "')";
            }
        }
        $value = $driver->getDataValue($value, $tinf);
        if (is_object($value) || is_array($value)) {
            igk_dev_wln_e(__FILE__ . ":" . __LINE__, $tinf->clName,  $value);
        }
        return "'" . $driver->escape_string($value) . "'";
    }

    protected static function IsAllowedDefValue($def, $type, $value)
    {
        if ($b = getv($def, strtoupper($type))) {
            if (isset($b[strtoupper($value)])) {
                return true;
            }
        }
        return false;
    }

    /**
     * get update array values
     * @param mixed $driver 
     * @param mixed $values 
     * @param mixed $tableInfo 
     * @param int $update 
     * @return mixed 
     * @throws IGKException 
     */
    protected static function GetValues($driver, $values, &$tableInfo, $update = 0)
    {
        $tvalues = new stdClass();

        if (is_object($values) && method_exists($values, "to_array")) {
            $values = $values->to_array();
        }
        if (is_object($values)) {
            $values = SQLObjectDef::Resolve($values, !$update);
        }
        if (is_array($values))
            $values = (object)$values;

        if ($tableInfo) {
            $filter = $driver->getFilter();
            $keys = [];
            foreach ($tableInfo as $k => $v) {
                $pv = '';
                if (is_numeric($k)) {
                    $k = $v->clName;
                }
                // if ($k == 'clLastLogin'){
                //     igk_wln('last login', __FILE__.":".__LINE__);
                // }
                if (isset($keys[$k])) {
                    igk_die('column key already defined :' . $k);
                }
                $keys[$k] = $v;
                if (!is_object($v)) {
                    igk_trace();
                    igk_wln_e(__FILE__ . ":" . __LINE__, 'not an object, ',  $v);
                }
                if ($v->clIsPrimary && $filter) {
                    continue;
                }
                if ($update) {
                    if (!empty($v->clUpdateFunction)) {
                        // to auto generate valeu to update
                        if ($v->clUpdateFunction == "IGK_PASSWD_ENCRYPT") {
                            if (property_exists($values, $k)) {
                                if (!empty($values->$k)) {
                                    $tvalues->$k = $values->$k;
                                }
                            }
                            continue;
                        }
                        $tvalues->$k = null;
                        continue;
                    }
                }


                if (is_object($values) && !property_exists($values, $k)) {
                    if ($update) {
                        if (
                            $v->clLinkType ||
                            !$v->clUpdateFunction ||
                            !preg_match("/(date|datetime)/i", $v->clType)
                        ) {
                            continue;
                        }
                    }
                    if ($driver->filterColumn($v, null)) {
                        continue;
                    }
                    if ($v->clNotAllowEmptyString) {
                        igk_die("value passed to $k is an empty string");
                    }
                    $tvalues->$k = null;
                } else {
                    if (empty($values->{$k}) && $v->clNotAllowEmptyString) {
                        igk_die("value passed to $k is an empty string");
                    }
                    $pv = $values->{$k};
                    // primary type
                    if (strtolower($v->clType) == 'enum') {
                        $pv = '' . $pv;
                    }
                    if (strtolower($v->clType) == 'datetime') {
                        if (empty($pv)) {
                            if ($v->clNotNull) {
                                $pv = 'NULL';
                            } else {
                                $pv = null; // $v->clDefault ?? 'CURRENT_TIME_SPAN'; //NOW()';//2000-01-01 00:00:00';
                            }
                        }
                    }

                    $tvalues->$k = $pv;
                }
            }
            // update keys
            $tableInfo = $keys;
        } else {
            $tvalues = $values;
        }
        return $tvalues;
    }
    /**
     * build select query
     * @param string $tbname 
     * @param mixed|null $where 
     * @param mixed|null $options 
     * @return string 
     * @throws IGKException 
     */
    public function createSelectQuery(string $tbname, $where = null, $options = null)
    {
        $q = "";
        $ad = $this->m_driver;
        $db = $this->m_driver->getDbName();

        if ($options == null) {
            $options = db_create_options();
        } else if (is_callable($options)) {
            $g = db_create_options();
            $c = self::CALLBACK_OPTS;
            $g->$c = $options;
            $options = $g;
        }
        if ($where != null) {
            $sq = "";
            if (!is_numeric($where) && is_string($where)) {
                $sq .= $where;
            } else {
                $operand = getv($options, "Operand", "AND");
                $sq .= static::GetCondString($this->m_driver, $where, $operand, $ad);
            }
            $sq = trim($sq);
            if (!empty($sq)) {
                $q .= " WHERE " . $sq;
            }
        }
        $tq = static::GetExtraOptions($options, $this->m_driver);
        $column = $tq->columns;
        if (!empty($tq->join)) {
            $q = " " . rtrim($tq->join) . " " . ltrim($q);
        }
        if (isset($tq->extra)) {
            $q .= " " . $tq->extra;
        }
        $flag = "";
        //if ($ad->querydebug) {
        $flag = getv($tq, "flag");
        //}
        if (strpos($tbname, '.') !== false) {

            //if (strpos($db, $tbname)){
            $tbname = self::EscapeTableName($tbname, $ad);
            //}
        } else {
            $tbname = $ad->escape_table_column($tbname);
        }

        $q = "SELECT {$flag}{$column} FROM " . $tbname . "" . rtrim($q) . ";"; // ".$tq->extra;
        return $q;
    }
    public static function EscapeTableName($tbname, $ad)
    {
        return implode(".", array_map(function ($i) use ($ad) {
            return $ad->escape_table_column($i);
        }, explode(".", $tbname)));
    }
    /**
     * resolv query condition string
     * @param mixed $driver 
     * @param mixed $tab 
     * @param string $operator 
     * @param mixed $adapter  
     * @return mixed 
     */
    public static function GetCondString($driver, $tab, $operator = 'AND', $primaryKey = IGK_FD_ID)
    {
        $query = "";
        $t = 0;
        $fc = "getValue";
        $to = "obj:type";
        $adapter = $driver;
        $op = $adapter->escape_string($operator);
        $c_exp = "IS NULL";
        if (is_numeric($tab)) {
            return $driver->escape_table_column($primaryKey) . "='{$tab}'";
        }
        if (is_object($tab) && ($r = $adapter->getObjValue($tab))) {
            return $r;
        }
        if (is_object($tab) && ($tab instanceof \IGK\Database\DbQueryCondition)) {
            $op = $tab->operand;
            $tab = $tab->to_array();
        }

        $qtab = [["tab" => $tab, "operator" => $op, "query" => &$query]];
        $loop =  0;
        $tquery = [];
        while ($ctab = array_shift($qtab)) {
            if (!$loop) {
                $loop = 1;
            } else {
                //$query .= " $op ";
                $t = 0;
            }

            //igk_wln("entry one 1...".$op, array_keys($tab));
            $tab = $ctab["tab"];
            $query = &$ctab["query"];
            $tquery[] = &$query;
            foreach ($tab as $k => $v) {
                $op = $ctab["operator"];
                $c = "=";
                if (is_numeric($k)) {
                    if (is_array($v) && count($v) == 2) {
                        $k = $v[0];
                        $v = $v[1];
                    }
                }
                if (is_object($v)) {

                    if ($v instanceof \IGK\Database\DbQueryCondition) {
                        $q = self::GetCondString($driver, $v);
                        $query .= sprintf("(%s)", $q);
                        $t = 1;
                        continue;
                    }

                    if ($r = $adapter->getObjValue($v)) {
                        if ($t == 1)
                            $query .= " $op ";


                        if (!is_numeric($k)) {

                            // TODO : evaluate object operator  
                            // + if (is_null($k = self::_GetKeyOperator($k, $v, $query,$c, $op, $t, $c_exp,$adapter))){
                            //     continue 2;
                            // } 
                            $r = "" . $driver->escape_table_column($k) . "=" . $r;
                        }
                        $query .= $r;
                        $t = 1;
                        continue;
                    }
                    // object that need to implement operand and condition properties
                    $tb = get_robjs("operand|conditions", 0, $v);

                    if ($tb->operand && $tb->conditions && preg_match("/(or|and)/i", $tb->operand)) {
                        $end = "";
                        if ($t) {
                            // $query .= " $op (";
                            // $end = ")";
                            $t = 0;
                        }
                        // $op = strtoupper($tb->operand);
                        array_unshift($qtab, [
                            "tab" => $tb->conditions,
                            "operator" => strtoupper($tb->operand),
                            "end" => $end,
                            "query" => ""
                        ]);
                        continue;
                    }
                }
                if ($t == 1)
                    $query .= " $op ";
                $v_is_obj = is_object($v);
                if ($v_is_obj && isset($v->$fc) && is_callable($v->$fc)) {
                    $query .= "`" . $v->$fc() . "`";
                } else {
                    if ($v_is_obj) {
                        $v = json_encode($v);
                    }
                    if (is_null($k = self::_GetKeyOperator($k, $v, $query, $c, $op, $t, $c_exp, $adapter))) {
                        continue 2;
                    }
                    $query .= static::GetKey($k, $adapter);
                    if ($v !== null) {
                        if (is_array($v)) {
                            $query .= $c;
                            if ($op == 'in') {
                                $query .= "(" . implode(", ", $v) . ")";
                            } else
                                $query .= implode(" ", $v);
                        } else {
                            $query .= "{$c}'" . $adapter->escape_string($v) . "'";
                        }
                    } else
                        $query .= " " . $c_exp;
                }
                $t = 1;
            }
        }

        $tquery = array_filter($tquery);
        if (count($tquery) > 1) {
            $query = "(" . implode(") {$operator} (", $tquery) . ")";
        }
        return $query;
    }

    protected static function _GetKeyOperator($k, $v,  &$query, &$c, &$op, &$t, &$c_exp, $adapter)
    {
        if (preg_match("/^(!|@@|@&|(<|>)=?|#|\||&)/", $k, $tab)) {
            $ch = substr($k, 0, $ln = strlen($tab[0]));
            $k = substr($k, $ln);
            $op = null;
            switch ($ch) {
                case '!':
                    $c = "!=";
                    $c_exp = "IS NOT NULL";
                    break;
                case "@@";
                    $c = " Like ";
                    break;
                case "@&":
                    $query .= "(" . static::GetKey($k, $adapter) . " & " . $adapter->escape_string($v) . ") = " . $adapter->escape_string($v);
                    $t = 1;
                    return null;
                case "#":
                    $c = " In ";
                    $op = "in";
                    break;
                default:
                    $c = $ch;
                    break;
            }
        }
        return $k;
    }

    protected static function GetKey($k, $driver)
    {
        return  implode(".", array_map([$driver, "escape_table_column"], explode(".", $k)));
    }

    /**
     * retrieve join type 
     * @param string $type 
     * @return string 
     */
    public static function GetJointType(string $type)
    {
        $t = $type;
        switch (strtolower($type)) {
            case 'left':
                $t = "LEFT JOIN";
                break;
            case 'right':
                $t = "RIGHT JOIN";
                break;
        }
        return $t;
    }
    public static function BuildColumn($v, $ad, $append = false)
    {
        $columns = '';
        if (!is_array($v))
            $v = [$v];

        foreach ($v as $k => $s) {
            if ($append) {
                $columns .= ", ";
            }
            $append = 1;
            if (is_string($k) && is_string($s)) {
                if (empty($k)) die("column key not allowed");
                // string case
                $columns .= $k;
                if ($k != $s)
                    $columns .= " AS " . $s;
                continue;
            }
            if (is_string($s)) {
                $columns .= $ad->escape_string($s);
            } elseif (is_object($s)) {
                // object of db expression;
                if ($rg = $ad->getObExpression($s, true)) {
                    $columns .= $rg;
                }
            } elseif (isset($s["key"])) {
                $columns .= $ad->escape_string($s["key"]);
            } elseif (isset($s["func"]) && isset($s["args"])) {
                if (is_array($s["args"])) {
                    $columns .= $s["func"] . "(" . implode(', ', $s["args"]) . ")";
                } else {
                    $columns .= $s["func"] . "(" . $s["args"] . ")";
                }
            } elseif (is_array($s) && (count($s) == 1) && is_string($s[0])) {
                $columns .= $s[0];
            }
            if ($c = getv($s, "as")) {
                $columns  .= " AS " . $c;
            }
        }
        return $columns;
    }
    ///<summary></summary>
    ///<param name="options"></param>
    /**
     * Order query extra options
     * @param mixed $options
     */
    protected static function GetExtraOptions($options, $ad)
    {
        $defOrder = "ASC";
        $q = "";
        $options = !is_object($options) ? (object)$options : $options;
        $optset = [];
        $columns = "*";
        $query = "";
        $flag = "";
        $join = "";
        // $_express = function ($v, &$query) use ($defOrder) {
        //     $a = 0;
        //     foreach ($v as $s) {
        //         $s_t = explode("|", $s);
        //         if ($a)
        //             $query .= ",";
        //         $query .= $s_t[0] . " " . strtoupper(getv($s_t, 1, $defOrder));
        //         $a = 1;
        //     }
        // };
        $_buildjoins = function ($v, &$join) {
            if (!is_array($v)) {
                die("join options not an array");
            }
            foreach ($v as $m) {
                if (empty($m)) continue;
                $t = "INNER JOIN";
                if (!is_array($m)) {
                    die("expected array list in joint: " . $m);
                }
                $tab = array_keys($m)[0];
                $vv = array_values($m)[0];
                if ($v_type = igk_getv($vv, "type")) {
                    $t =  static::GetJointType($v_type);
                }
                $join .= $t . " ";
                $join .= $tab . " ";
                if ($alias = igk_getv($vv, 'alias')){
                    $join .= 'as '.$alias.' ';
                }
                $v_cond = igk_getv($vv, 0);
                if ($v_cond) {
                    if (is_string($v_cond)) {
                        $join .= "on (" . $v_cond . ") ";
                    } else {
                        die("condition not allowed");
                    }
                }
            }
        };
        $t_data = get_robjs("Distinct|GroupBy|OrderBy|OrderByField|Columns|Limit|Joins", 0, $options);
        foreach ($t_data as $k => $v) {
            if (!$v) continue;
            switch ($k) {
                case queryConstant::Distinct:
                    $flag .= "DISTINCT ";
                    break;
                case queryConstant::Limit:
                    $optset[$k] = 1;
                    $h = 1;
                    if (is_array($v)) {
                        if (isset($v["start"]) && isset($v["end"])) {
                            $s = $v["start"];
                            $e = $v["end"];
                            $h = $s . ", " . $e;
                        } else if (count($v) == 1) {
                            $h = $v[0];
                        } else if (count($v) == 2) {
                            $h = $v[0] . "," . $v[1];
                        }
                    } else {
                        if (is_numeric($v) || is_string($v))
                            $h = $v;
                        else if (is_string($v)) {
                            $h = $v;
                        }
                    }
                    $optset[$k] = $h;
                    // $query .= " Limit " . $h;
                    break;
                case queryConstant::Joins:
                    $_buildjoins($v, $join);
                    break;
                case queryConstant::GroupBy:
                    $optset[$k] = 1;
                    if ($ad->supportGroupBy()) {
                        $g_by = '';
                        $a = 0;
                        foreach ($v as $s) {
                            $s_t = explode("|", $s);
                            if ($a)
                                $g_by .= ",";
                            $g_by .= $s_t[0];
                            $a = 1;
                        }
                        if ($a)
                            $query .= sprintf("GROUP BY %s", $g_by);
                    }
                    break;
                case "OrderByField":
                    break;
                case queryConstant::OrderBy:  
                    if (is_array($v)) {
                        $torder = "";
                        $c = "";
                        foreach ($v as $s) {
                            $g = explode("|", $s);
                            $type = getv($g, 1, $defOrder);
                            $c = self::GetGroupKey($g[0], $type, $ad);
                            if (!empty($torder))
                                $torder .= ", ";
                            $torder .= $c;
                        }
                        $torder .= " ";
                        $optset[$k] = $torder;
                    } else {
                        fdie("OrderBy must be an array ['Column,...|Type']");
                    }
                    break;
                case "Columns":
                    //["func" => "CONCAT", "args"=> ["nom", "prenom"], "as" => "Charles"]
                    $a = 0;
                    $columns = "";
                    $columns .= self::BuildColumn($v, $ad, $a);
                    break;
            }
        }


        if (!isset($optset["OrderBy"])) {

            if (isset($options->Sort) && isset($options->SortColumn)) {
                $v = strtoupper($options->Sort);
                if (strpos("ASC|DESC", $v) !== false) {
                    $q .= " ORDER BY `" . $ad->escape_string($options->SortColumn) . "` " . $v;
                    $optset["OrderBy"] = 1;
                }
            } else {

                if (isset($options->SortColumn) &&  @is_array($options->SortColumn)) {
                    foreach ($options->SortColumn as $r => $v) {
                        $v = strtoupper($v);
                        if (strpos("ASC|DESC", $v) !== false) {
                            $q .= " ORDER BY `" . $ad->escape_string($r) . "` " . $v;
                            $optset["OrderBy"] = 1;
                        }
                    }
                }
            }
        } else {
            $q .= "ORDER BY " . $optset["OrderBy"];
        }

        if (!isset($optset["Limit"])) {
            if (is_numeric($limit = getv($options, "Limit"))) {
                $lim = $ad->escape_string($limit);
                if (is_numeric($offset = getv($options, "LimitOffset"))) {
                    $lim = $ad->escape_string($offset) . ", " . $lim;
                }
                $query .= " Limit " . $lim;
            }
        } else {
            $query .= " Limit " . $optset['Limit'];
        }
        $query = trim($query);

        return (object)["columns" => $columns, "join" => $join, "extra" => $q . $query, "flag" => $flag];
    }

    protected static function Key($t, $adapter, $separator = ",")
    {
        return implode($separator, array_map(
            function ($t) use ($adapter) {
                return  "`" . implode("`.`", array_map([$adapter, "escape_string"], explode(".", $t))) . "`";
            },
            array_map("trim", array_filter(explode(",", $t)))
        ));
    }



    protected static function GetGroupKey($columns, string $type, $adapter):string
    {
        return implode(' '.$type.',', array_map(
            function ($t) use ($adapter) {
                return  "`" . implode("`.`", array_map([$adapter, "escape_string"], explode(".", $t))) . "`";
            },
            array_map("trim", array_filter(explode(",", $columns)))
        )). ' '.$type;
    }

    ///<summary>get column query definition</summary>
    /**
     * get column query definition
     */
    // public function getColumnDefinition($v, $nocomment = 0):string
    // {
    //     $driver = $this->m_driver;
    //     $query = "";
    //     $type = igk_getev($v->clType, "Int");
    //     $query .= $driver->escape_string($type);
    //     $s = strtolower($type);
    //     $number = false;
    //     $tinf = null;
    //     if (isset(static::$LENGTHDATA[$s])) {
    //         if ($v->clTypeLength > 0) {
    //             $number = true;
    //             $query .= "(" . $driver->escape_string($v->clTypeLength) . ") ";
    //         } else
    //             $query .= " ";
    //     } else
    //         $query .= " ";
    //     if (!$number) {
    //         if (($v->clNotNull) || ($v->clAutoIncrement))
    //             $query .= "NOT NULL ";
    //         else
    //             $query .= "NULL ";
    //     } else if ($v->clNotNull) {
    //         $query .= "NOT NULL ";
    //     }
    //     if ($v->clAutoIncrement) {
    //         $query .= $this->m_driver->getParam("auto_increment_word", $v, $tinf) . " ";
    //     }
    //     $tb = true;
    //     if ($v->clDefault || $v->clDefault === '0') {
    //         $query .= "DEFAULT '" . $driver->escape_string($v->clDefault) . "' ";
    //     }
    //     if ($v->clDescription && !$nocomment) {
    //         $query .= "COMMENT '" . $driver->escape_string($v->clDescription) . "' ";
    //     }
    //     return $query;
    // }

    public function createDeleteQuery($tbname, $condition = null)
    {
        $c = "";

        if ($condition && ($c = static::GetCondString($this->m_driver, $condition))) {
            $c = " WHERE " . $c;
        }
        return "DELETE FROM `" . $this->m_driver->escape_string($tbname) . "`" . $c . ";";
    }
    public function listTables()
    {
        $tables = [];
        $col = "Tables_in_" . $this->m_driver->getDbName();
        $this->m_driver->sendQuery("SHOW TABLES;", true, [
            IGKMySQLQueryResult::CALLBACK_OPTS => function ($row) use (&$tables, $col) {
                $tables[] =  (object)["table" => $row->{$col}];
                return false;
            }
        ]);
        return $tables;
    }
    /**
     * get column string concatenation
     */
    public static function GetColumnString(array $s)
    {
        return implode(", ", array_map(function ($a, $k) {
            if ($a == $k) {
                return $a;
            }
            return $k . " as " . $a;
        }, $s, array_keys($s)));
    }
    //retrieve loaded relation
    /**
     * 
     * @param string $table 
     * @param mixed $field 
     * @param string $dbname 
     * @return ?object 
     * @throws IGKException 
     */
    public function get_relation(string $table, $field, string $dbname)
    {
        // igk_die(__METHOD__ . " not implements");
        return [];
    }
    /**
     * get column info
     * @param string $table 
     * @param string $dbname 
     * @return mixed 
     * @throws IGKException 
     */
    public function get_column_info(string $table, string $column)
    {
        $db = $this->m_driver->getDbName();
        $query = $this->m_driver->createTableColumnInfoQuery($this, $table, $column, $db);

        $res = $this->m_driver->sendQuery($query);
        if ($res) {
            if ($res = $res->getRowAtIndex(0)) {
                return $res->to_array();
            }
        }
        return $res;
    }

    public function createSelectExpression($table_name, $column, $conditions)
    {
        $g = $this->createSelectQuery($table_name, $conditions, [
            "Columns" => $column
        ]);
        return new DbExpression("(" . rtrim($g, ";") . ")");
    }
}
