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
use Exception;
use IGK\Constants;
use IGK\Database\DbColumnInfo;
use IGK\Database\DbExpression;
use IGK\Database\DbLitteralExpression;
use IGK\Database\DbSchemas;
use IGK\Database\IDataDriver;
use IGK\Database\IDbColumnInfo;
use IGK\Database\IDbEntryDefinition;
use IGK\Database\SQLQueryUtils;
use IGK\Helper\Database;
use IGK\Helper\StringUtility;
use IGK\Models\ModelBase;
use IGK\System\Console\Logger;
use IGK\System\Database\Exceptions\SQLGrammarException;
use IGK\System\Database\MySQL\IGKMySQLQueryResult;
use IGK\System\Database\QueryBuilderConstant as queryConstant;
use IGK\System\IO\Configuration\ConfigurationReader;
use IGKException;
use IGKSysUtil;
use stdClass;

/**
 * represent sql default grammar. Root is mysql behaviour
 * @package IGK\System\Database
 */
class SQLGrammar implements IDbQueryGrammar
{
    /**
     * auto generate doc.
     * @var IDataDriver
     */
    private $m_driver;
    /**
     * auto generate doc.
     * @return mixed
     */
    public function getVersion()
    {
        return $this->getEngineVersion() ?? $this->getDriverVersion();
    }
    /**
     * retrieve engine version
     * @return null 
     * @throws Exception 
     */
    protected function getEngineVersion()
    {
        return null;
    }
    /**
     * Returns Driver Version.
     */
    public function getDriverVersion()
    {
        return $this->m_driver->getVersion();
    }
    /**
     * Constant: fd id.
     * @var mixed
     */
    const FD_ID = "clId";
    /**
     * Constant: callback opts.
     * @var mixed
     */
    const CALLBACK_OPTS = \IGK\Database\DbConstants::CALLBACK_OPTS;
    /**
     * Constant: and op.
     * @var mixed
     */
    const AND_OP = 'AND';
    /**
     * set SQL driver to use
     * @param mixed $driver 
     * @return void 
     */
    public function setDriver($driver)
    {
        $this->m_driver = $driver;
    }
    /**
     * destructor
     * @param mixed $n
     * @param mixed $v
     */
    public function __set($n, $v)
    {
        if (method_exists($this, $fc = "set" . $n)) {
            $this->$fc($v);
        }
    }
    /**
     * Constant: avail func.
     * @var mixed
     */
    const AVAIL_FUNC = [
        'IGK_PASSWD_ENCRYPT',
        'AES_ENCRYPT',
        'BIN',
        'CHAR',
        'COMPRESS',
        'CURRENT_USER',
        'AES_DECRYPTDATABASE',
        'DAYNAME',
        'DES_DECRYPT',
        'DES_ENCRYPT',
        'ENCRYPT',
        'HEX',
        'INET6_NTOA',
        'INET_NTOA',
        'LOAD_FILE',
        'LOWER',
        'LTRIM',
        'MD5',
        'MONTHNAME',
        'OLD_PASSWORD',
        'PASSWORD',
        'QUOTE',
        'REVERSE',
        'RTRIM',
        'SHA1',
        'SOUNDEX',
        'SPACE',
        'TRIM',
        'UNCOMPRESS',
        'UNHEX',
        'UPPER',
        'USER',
        'UUID',
        'VERSION',
        'ABS',
        'ACOS',
        'ASCII',
        'ASIN',
        'ATAN',
        'BIT_COUNT',
        'BIT_LENGTH',
        'CEILING',
        'CHAR_LENGTH',
        'CONNECTION_ID',
        'COS',
        'COT',
        'CRC32',
        'CURRENT_DATE',
        'CURRENT_TIME',
        'DATE',
        'DAYOFMONTH',
        'DAYOFWEEK',
        'DAYOFYEAR',
        'DEGREES',
        'EXP',
        'FLOOR',
        'FROM_DAYS',
        'FROM_UNIXTIME',
        'HOUR',
        'INET6_ATON',
        'INET_ATON',
        'LAST_DAY',
        'LENGTH',
        'LN',
        'LOG',
        'LOG10',
        'LOG2',
        'MICROSECOND',
        'MINUTE',
        'MONTH',
        'NOW',
        'OCT',
        'ORD',
        'PI',
        'QUARTER',
        'RADIANS',
        'RAND',
        'ROUND',
        'SECOND',
        'SEC_TO_TIME',
        'SIGN',
        'SIN',
        'SQRT',
        'SYSDATE',
        'TAN',
        'TIME',
        'TIMESTAMP',
        'TIME_TO_SEC',
        'TO_DAYS',
        'TO_SECONDS',
        'UNCOMPRESSED_LENGTH',
        'UNIX_TIMESTAMP',
        'UTC_DATE',
        'UTC_TIME',
        'UTC_TIMESTAMP',
        'UUID_SHORT',
        'WEEK',
        'WEEKDAY',
        'WEEKOFYEAR',
        'YEAR',
        'YEARWEEK'
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
    /**
     * .ctr
     * @param IDataDriver $driver
     */
    public function __construct(IDataDriver $driver)
    {
        ($driver === null) && die("driver must setup");
        $this->m_driver = $driver;
    }
    /**
     * Allowed def value.
     */
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
    /**
     * Creates Expression.
     * @param DbExpression $expression
     */
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
     * -
     * @param string $d
     * @var IGK\System\Database\SplitColumnMemberRereference
     */
    public static function SplitColumnMemberRereference(string $d)
    {
        return array_filter(array_map('trim', preg_split('/,|-/', $d)), function ($a) {
            return strlen($a) > 0;
        });
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
            'boolean' => 'tinyint'
        ], $t = strtolower($t), $t);
    }
    /**
     * Fallback type.
     * @param mixed $t
     * @param mixed $adapter
     */
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
        return $this->m_driver->allowTypeLength($type, null);
    }
    /**
     * Removes foreign.
     * @param string $table
     * @param mixed $column
     * @return ?string
     */
    public function remove_foreign(string $table, $column): ?string
    {
        return $this->m_driver->remove_foreign($table, $column);
    }
    /**
     * create table query
     * @param string $tablename
     * @param mixed $tbname
     * @param mixed $desc_or_options
     * @param mixed $options
     * @param ?string $prefix
     * @param ?array $extra
     * @throws IGKException
     * @return string
     */
    public function createTablequery(string $tablename, array $columninfo, $desc_or_options = null, $options = null, ?string $prefix = null, ?array $extra = null)
    {
        $desc = '';
        $Engine = null;
        if (is_string($desc_or_options)) {
            $desc = $desc_or_options;
            $Engine = igk_getv($options, 'Engine');
        } else if (is_array($desc_or_options)) {
            list($desc, $prefix, $indexes, $Engine) = igk_extract($desc_or_options, 'description|prefix|indexes|Engine');
            $extra = compact('prefix', 'indexes');
        }
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
        $findexes = "";
        $uniques = array();
        $primkey = "";
        $tinf = array();
        $defvalue = static::AllowedDefValue();
        $resovlType = igk_environment()->getResolvSQLType();
        $support = $driver->getEngineSupport();
        $engine_name = $Engine ?? ($support ? 0 : null);
        $nocomment = 0;
        $length_regex = DbColumnInfo::TYPE_LENGTH_REGEX;
        $v_real_column_names = [];
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
            $v_type = $v->clType;
            if (preg_match($length_regex, $v_type, $tinfo)) {
                if (!$v->clTypeLength || ($v->clTypeLength != $tinfo['size'])) {
                    $v->clTypeLength = intval($tinfo['size']);
                }
                $v_type = trim(preg_replace($length_regex, '', $v_type));
            }
            $v_real_column_names[$v_name] = $v_name;
            $primkey = "noprimkey://" . $v_name;
            $v_name = $driver->escape_string($v_name);
            $query .= "" . self::GetKey($v_name,  $driver) . " ";
            $type = getev(static::ResolvType($v_type), "Int");
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
                    if ($g = self::GetEnumQueryValueQueryString($e_ev, $driver)) {
                        $e_sv = 1;
                        $query .= '(' . $g . ')';
                    }
                }
                if (!$e_sv)
                    $query .= "(" . implode(",", array_map(function (string $i) use ($driver) {
                        return "'" . $driver->escape_string($i) . "'";
                    }, array_filter(explode(',', $e_ev), function (string $c) {
                        return (strlen(trim($c)) > 0);
                    }))) . ")";
            }
            $query .= " ";
            if (!empty($v->clLinkType)) {
                $driver->pushRelations($tablename, $v);
            }
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
            if ($v->clCharset) {
                $query .= $driver->queryColumnCharset($v->clCharset);
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
                }
            }
            if ($v->clDescription && !$nocomment) {
                $query .= "COMMENT '" . $this->m_driver->escape_string($v->clDescription) . "' ";
            }
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
                    $p = $v->clColumnMemberIndex;
                    if (is_array($p)) {
                        $p = implode(',', $p);
                    }
                    $v_unique_columns_index = '' . $p;
                }
                $tindex = self::SplitColumnMemberRereference($v_unique_columns_index);
                $indexes = array();
                foreach ($tindex as $kindex) {
                    if (!is_numeric($kindex) || isset($indexes[$kindex]))
                        continue;
                    $indexes[$kindex] = 1;
                    $ck = 'unique_' . $kindex;
                    $bf = "";
                    if (!isset($uniques[$ck])) {
                        $ts =  Database::AutoPrefixColumn("UC_" . $ck . "_index", $prefix);
                        $bf .= "UNIQUE KEY `" . $ts . "`(`" . $v_name . "`";
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
                $v_nk = $v_name;
                if ($v->clLinkType) {
                    // + | --------------------------------------------------------------------
                    // + | add _FK_ to indicate possible Foreign key 
                    // + |                    
                    $v_nk .= '_FK';
                }
                if (!empty($findex))
                    $findex .= ",";
                $findex .= "KEY `" . $v_nk . "_index` (`" . $v_name . "`)";
            }
            unset($tinf[$primkey]);
        }
        if ($extra) {
            (function ($extra, $columns) use (&$findexes, $prefix) {
                list($indexes, $prefix) = igk_extract($extra, 'indexes|prefix');
                $g = [];
                if ($indexes) {
                    foreach ($indexes as $k) {
                        if (!empty($cl = self::CheckColumn($k->columns, $columns, $prefix, $error))) {
                            $key = '';
                            $key .= $k->isUnique ? 'UNIQUE ' : '';
                            $g[] = sprintf($key . 'INDEX ' . $k->name . ' (%s)', $cl);
                        } else {
                            throw new \IGK\System\Database\Exceptions\SQLGrammarException('missing column reference in indices');
                        }
                    }
                    $findexes = implode(",\n", $g);
                }
            })($extra, $v_real_column_names);
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
        if (!empty($findexes)) {
            $query .= ", " . $findexes;
        }
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
        $query = sprintf($driver->getCreateTableFormat(["checkTable" => 1]), trim($query));
        return $query;
    }
    /**
     * auto generate doc.
     * @param string $column
     * @param array $columns
     * @param null|string $prefix
     * @param null|mixed & $error
     * @return string
     */
    private static function CheckColumn(string $column, array $columns, ?string $prefix = null, &$error = null): string
    {
        $ct = explode(',', $column);
        $g = [];
        while (count($ct) > 0) {
            if ($q = trim(array_shift($ct))) {
                $r = $q;
                if (isset($columns[$q]) || ($prefix && isset($columns[$q = $prefix . $q]))) {
                    $g[] = $q;
                    continue;
                }
                $error[] = $r;
            }
        }
        return implode(',', $g);
    }
    /**
     * get enum value string from data definition
     * @param string $d
     * @param mixed $driver
     * @return null|string
     */
    public static function GetEnumQueryValueQueryString(string $d, $driver): ?string
    {
        if ($g = ConfigurationReader::ParseEnumLitteralValue($d)) {
            $t = [];
            foreach ($g as $k => $v) {
                $r = is_null($v) ? $k : $v;
                $t[] = "'" . $driver->escape_string($r) . "'";
            }
            return implode(',', $t);
        }
        return null;
    }
    /**
     * return null in case of foreign key exists in defined
     * @param string $table
     * @param IDbColumnInfo|array $column_info
     * @param mixed $nk
     * @param mixed $db
     */
    public function add_foreign_key(string $table, $column_info, $nk = null, $db = null)
    {
        $db = $db ?? $this->m_driver->getDbName();
        if (!empty($nk) || !empty($nk = igk_getv($column_info, "clLinkConstraintName", ""))) {
            if ($this->m_driver->constraintForeignKeyExists($nk)) {
                $nk = null;
                return;
            } else {
                $nk = "CONSTRAINT " . $nk . " ";
            }
        }
        $clkey = "%s(%s)";
        $tbname =   $this->joinTableName($table, $db);
        $cl = $column_info->clLinkType;
        $link = $this->joinTableName($cl, $db);
        $link_column =  getv($column_info, "clLinkColumn", self::FD_ID);
        $query = sprintf(
            $this->m_driver->createAlterTableFormat(),
            $tbname,
            $nk,
            $column_info->clName,
            sprintf(
                $clkey,
                $link,
                $this->m_driver->escape_table_column(
                    $link_column
                )
            )
        );
        return $query;
    }
    /**
     * joint table table's name
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
     * auto generate doc.
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
    /**
     * query unique  unit key
     * @param string $table
     * @param array $columns
     * @param ?string $id
     * @return string
     */
    public function addUnique(string $table, $columns, ?string $id = null)
    {
        $id = $id ? sprintf('`%s`', $this->m_driver->escape_string($id)) : null;
        return sprintf('ALTER TABLE `%s` ADD UNIQUE %s(`%s`);', $table, $id, implode('`,`', $columns));
    }
    /**
     * Drops All Unique Contraints.
     * @param string $table
     */
    public function dropAllUniqueContraints(string $table) {}
    /**
     * auto generate doc.
     * @param mixed $column
     * @return mixed
     */
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
    /**
     * Drops index.
     * @param string $table
     * @param mixed $column
     * @return ?string
     */
    public function drop_index(string $table, $column): ?string
    {
        if (!$column) {
            return null;
        }
        $idx = null;
        if (strtolower($column) == 'primary') {
            $idx = "PRIMARY";
        }
        $column = $this->_get_column_list($column);
        $idx = $idx ?? strtolower('IDX_' . StringUtility::CamelClassName($column));
        $q = "DROP INDEX ";
        $q .= $idx . " ON `" . $table . "`;";
        return $q;
    }
    /**
     * create add column alter query
     * @param mixed $table table 
     * @param mixed $info column info
     * @param mixed $after after columns
     * @return string 
     */
    public function add_column(string $table, $info, ?string $after = null)
    {
        Logger::warn('try add column: ' . $table . ' :-> ' . $info->clName);
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
        $version = $this->getVersion();
        if ($adapter->getType() == IGK_MYSQL_DATAADAPTER) {
            $q = "ALTER TABLE ";
            if (version_compare($version, '8.0', '>=')) {
                $q .= "`" . $table . "` RENAME COLUMN ";
                $q .= $adapter->escape($column) . " TO " . $adapter->escape($new_name) . ';';
            } else {
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
     * auto generate doc.
     * @param string $table
     * @param object $info
     * @param null|string $new_name
     * @return ?string
     */
    public function change_column(string $table, object $info, ?string $new_name = null)
    {
        igk_debug_wln("change_column grammar: " . $table);
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
    /**
     * Drops foreign key.
     * @param mixed $table
     * @param mixed $info
     */
    public function drop_foreign_key($table, $info)
    {
        Logger::warn('drop foreign key');
    }
    /**
     * Drops column.
     * @param mixed $table
     * @param mixed $column
     */
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
        return in_array($s, ['int', 'float', 'decimal']);
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
            }, array_filter(explode(',', $v->clEnumValues), function ($c) {
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
        if ($v->clCharset) {
            $query .= sprintf('CHARSET %s ', $v->clCharset);
        }
        if ($v->clDescription && !$nocomment) {
            $query .= "COMMENT '" . $adapter->escape_string($v->clDescription) . "' ";
        }
        return $query;
    }
    /**
     * auto prefix values keys
     * @param mixed &$values 
     * @param string $prefix 
     * @return void 
     */
    public static function AutoPrefixValue(&$values, string $prefix)
    {
        foreach (array_keys($values) as $k) {
            if (($tk = StringUtility::AutoPrefix($k, $prefix)) != $k) {
                $values[$tk] = $values[$k];
                unset($values[$k]);
            }
        }
    }
    /**
     * auto generate doc.
     * @param string $tbname
     * @param mixed $values
     * @param mixed $tableInfo
     * @return ?string
     */
    public function createInsertQuery(string $tbname, $values, $tableInfo = null): ?string
    {
        if ($tableInfo === null) {
            list($p, $prefix) = igk_extract(igk_db_get_table_info($tbname), 'ColumnInfo|prefix');
            $tableInfo = $p;
            if (is_array($values) && $prefix) {
                // + | tranform to compatible info
                self::AutoPrefixValue($values, $prefix);
            }
        }
        $rtbname = $this->m_driver->escape_string($tbname);
        $_tbname = $this->m_driver->escape_table_name($rtbname);
        $query = "INSERT INTO " . $_tbname . "(";
        $v_v = "";
        $v_c = 0;
        if ($values instanceof IDbEntryDefinition) {
            $values = $values->getEntryValues();
        }
        $tvalues = static::GetValues($this->m_driver, $values, $tableInfo);
        foreach ($tvalues as $k => $v) {
            if ($v_c != 0) {
                $query .= ",";
                $v_v .= ",";
            } else
                $v_c = 1;
            $query .= "" . $this->m_driver->escape_table_column($k) . "";
            if ($tableInfo) {
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
     * auto generate doc.
     * @param string $tbname
     * @param mixed $values
     * @param mixed $condition
     * @param mixed|null $tableInfo columns info to build the query
     * @param ?bool $filter
     * @return string
     */
    public function createUpdateQuery(string $tbname, $values, $condition = null, $tableInfo = null, ?bool $filter = null): ?string
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
        $tvalues = static::GetValues($this->m_driver, $values, $tableInfo, 1, $filter);
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
    /**
     * Returns true if Unsigned.
     * @param mixed $v
     */
    public static function IsUnsigned($v)
    {
        if (method_exists($v, "IsUnsigned")) {
            return $v->IsUnsigned();
        }
        return false;
    }
    /**
     * auto generate doc.
     * @param mixed $driver
     * @param mixed $tbname
     * @param IDbColumnInfo $tinf
     * @param mixed $columnName
     * @param mixed $value
     * @param mixed $type
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
        if (is_string($value) && $driver->getIsLengthData($tinf->clType) && (strlen($value) > $tinf->clTypeLength)) {
            if ($tinf->clNoTrimExceed) {
                throw new IGKException('data too loog');
            }
            $value = substr($value, 0, $tinf->clTypeLength);
        }
        $is_json = strtolower($tinf->clType) == 'json';
        if (!$is_json && (is_integer($value))) {
            if (($value === 0) && !empty($tinf->clLinkType) && !$tinf->clNotNull) {
                return 'NULL';
            }
            if (($value === 0) && !empty($tinf->clLinkType) && $tinf->clNotNull) {
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
        if ($is_json) {
            if (is_string($value)) {
                $deco = json_decode($value);
                if (!json_last_error()) {
                    //$value = json_encode($value);
                    //igk_die("value not a valid json");
                    return "'" . $driver->escape_string(str_replace('\\"', '\\\\"', json_encode($deco, JSON_UNESCAPED_SLASHES))) . "'";
                }
            } else {
                if (($data = json_encode($value, JSON_UNESCAPED_SLASHES)) || ($data == '0')) {
                    //return "'-" . str_replace('\\"', '\\\\"', $data) . "'"; 
                    return "'" . $data. "'";
                }
            }
        }
        $of = 'NULL';
        $s = null;
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
                        return "'{}'";
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
                            return "'" . $driver->escape_string(date(Constants::MYSQL_DATETIME_FORMAT)) . "'";
                        case "date":
                            return "'" . $driver->escape_string(date(Constants::MYSQL_DATE_FORMAT)) . "'";
                        case "time":
                            return "'" . $driver->escape_string(date(Constants::MYSQL_TIME_FORMAT)) . "'";
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
        if ($value instanceof DbQueryExpression) {
            return $value->getValue();
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
        if ($is_json || is_object($value) || is_array($value)) {            
            $grammar = $driver->getGrammar();
            $value = $grammar->dataToJson($value);                
            return sprintf("'%s'", addslashes($value)); 
        }
        return "'" . $driver->escape_string($value) . "'";
    }
    /**
     * return a json string
     * @param mixed $data 
     * @return string|false 
     */
    public function dataToJson($data)
    {
        return json_encode($data, JSON_UNESCAPED_SLASHES);
    }
    /**
     * Returns true if Allowed Def Value.
     * @param mixed $def
     * @param mixed $type
     * @param mixed $value
     */
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
     * @param mixed & $tableInfo
     * @param mixed $tableInfo
     * @param ?bool $filter
     * @throws IGKException
     * @return mixed
     */
    protected static function GetValues($driver, $values, &$tableInfo, $update = 0, ?bool $filter = null)
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
            $filter = $filter ?? $driver->getFilter();
            $keys = [];
            foreach ($tableInfo as $k => $v) {
                $pv = '';
                if (is_numeric($k)) {
                    $k = $v->clName;
                }
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
                    if (strtolower($v->clType) == 'enum') {
                        $pv = '' . $pv;
                    }
                    if (strtolower($v->clType) == 'datetime') {
                        if (empty($pv)) {
                            if ($v->clNotNull) {
                                $pv = 'NULL';
                            } else {
                                $pv = null;
                            }
                        }
                    }
                    $tvalues->$k = $pv;
                }
            }
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
     * @param mixed|null $inf extra info 
     * @return string 
     * @throws IGKException 
     */
    public function createSelectQuery(string $tbname, $where = null, $options = null): ?string
    {
        $q = "";
        $ad = $this->m_driver;
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
                $operand = getv($options, "Operand", self::AND_OP);
                $sq .= static::GetCondString($this->m_driver, $where, $operand);
            }
            $sq = trim($sq);
            if (!empty($sq)) {
                $q .= " WHERE " . $sq;
            }
        }
        $tq = static::GetExtraOptions($options, $this->m_driver);
        $column = $tq->columns;
        if (!empty($tq->join)) {
            $q = " " . rtrim($tq->join . " " . ltrim($q));
        }
        if (isset($tq->extra)) {
            $q .= " " . $tq->extra;
        }
        $flag = "";
        $flag = getv($tq, "flag");
        if (strpos($tbname, '.') !== false) {
            $tbname = self::EscapeTableName($tbname, $ad);
        } else {
            $tbname = $ad->escape_table_column($tbname);
        }
        $q = "SELECT {$flag}{$column} FROM " . $tbname . "" . rtrim($q) . ";";
        return $q;
    }
    /**
     * Escape table name.
     * @param mixed $tbname
     * @param mixed $ad
     */
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
     * @param string|'AND'|'OR' $operator
     * @param ?string $primaryKey
     * @param mixed $tableInfo
     * @return mixed
     */
    public static function GetCondString($driver, $tab, $operator = 'AND', string $primaryKey = IGK_FD_ID, $tableInfo = null)
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
        if (is_object($tab)) {
            if ($tab instanceof \IGK\Database\DbQueryCondition) {
                $op = $tab->operand;
                $tab = $tab->to_array();
            } else if ($tab instanceof IDbWhereQueryCondition) {
                list($op, $tab) = $tab->getConditionInfo();
            } else {
                igk_die('invalid');
            }
        }
        $qtab = [["tab" => $tab, "operator" => $op, "query" => &$query]];
        $loop =  0;
        $tquery = [];
        while ($ctab = array_shift($qtab)) {
            if (!$loop) {
                $loop = 1;
            } else {
                $t = 0;
            }
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
                    } else if (is_string($v)) {
                        $query .= $v;
                        $t = 1;
                        continue;
                    } else if (is_array($v) && (count($v) == 1) && !is_numeric(key($v))) {
                        $k = key($v);
                        $v = $v[$k];
                    }
                }
                if ($v instanceof ModelBase) {
                    $v = $v->id();
                }
                if (is_object($v)) {
                    if ($v instanceof \IGK\Database\DbQueryCondition) {
                        if (!empty($q = self::GetCondString($driver, $v))) {
                            if ($t == 1)
                                $query .= " $op ";
                            $query .= sprintf("(%s)", $q);
                        }
                        $t = 1;
                        continue;
                    }
                    list($htab, $top) = igk_extract($v, 'list|operand');
                    if ($htab && $top) {
                        if (!empty($q = self::GetCondString($driver, $htab, $top))) {
                            if ($t == 1)
                                $query .= " $op ";
                            $query .= sprintf("(%s)", $q);
                        }
                        $t = 1;
                        continue;
                    }
                    if ($r = $adapter->getObjValue($v, $k, $tableInfo)) {
                        if ($t == 1)
                            $query .= " $op ";
                        if (!is_numeric($k)) {
                            // + if (is_null($k = self::_GetKeyOperator($k, $v, $query,$c, $op, $t, $c_exp,$adapter))){
                            $r = "" . $driver->escape_table_column($k) . "=" . $r;
                        }
                        $query .= $r;
                        $t = 1;
                        continue;
                    }
                    $tb = get_robjs("operand|conditions", 0, $v);
                    if ($tb->operand && $tb->conditions && preg_match("/(or|and)/i", $tb->operand)) {
                        $end = "";
                        if ($t) {
                            $t = 0;
                        }
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
                    $c_exp = null;
                    if (is_null($k = self::_GetKeyOperator($k, $v, $query, $c, $op, $t, $c_exp, $adapter))) {
                        continue;
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
                        $query .= " " . ($c_exp ?? 'IS NULL');
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
    /**
     * get operator 
     * @param mixed $k key to handle 
     * @param mixed $v value to express
     * @param mixed &$query 
     * @param mixed &$c 
     * @param mixed &$op 
     * @param mixed &$t 
     * @param mixed &$c_exp 
     * @param mixed $adapter 
     * @return mixed 
     */
    protected static function _GetKeyOperator($k, $v,  &$query, &$c, &$op, &$t, &$c_exp, $adapter)
    {
        if (preg_match("/^((?:!)?\<\>|!(!|<>)?|@@|@&|(?:<|>)=?|#|\||&)/", $k, $tab)) {
            $ch = substr($k, 0, $ln = strlen($tab[0]));
            $k = substr($k, $ln);
            $op = null;
            switch ($ch) {
                case SQLQueryFieldPrefixOperators::NOT_IN:
                    $c = " NOT IN ";
                    $query .= sprintf(
                        "%s NOT IN (%s)",
                        static::GetKey($k, $adapter),
                        rtrim($v, '; ')
                    );
                    $t = 1;
                    return null;
                case SQLQueryFieldPrefixOperators::IN_EXPRESS:
                case "!!":
                    if (is_array($v)) {
                        $v = implode(',', $v);
                    }
                    $c = " IN ";
                    $query .= sprintf(
                        "%s IN (%s)",
                        static::GetKey($k, $adapter),
                        rtrim($v, '; ')
                    );
                    $t = 1;
                    return null;
                case '!':
                    $c = "!=";
                    $c_exp = "IS NOT NULL";
                    break;
                case SQLQueryFieldPrefixOperators::FIND:
                    $c = " Like ";
                    break;
                case "@&":
                    $query .= "(" . static::GetKey($k, $adapter) . " & " . $adapter->escape_string($v) . ") = " . $adapter->escape_string($v);
                    $t = 1;
                    return null;
                case SQLQueryFieldPrefixOperators::IN:
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
    /**
     * Returns Key.
     * @param mixed $k
     * @param mixed $driver
     */
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
        if (QueryBuilderConstant::LeftJoin == $t) {
            return $t;
        }
        switch (strtolower($type)) {
            case 'left':
                $t = "LEFT JOIN";
                break;
            case 'right':
                $t = "RIGHT JOIN";
                break;
            case 'join':
                $t = 'INNER JOIN';
                break;
            default:
                throw new SQLGrammarException("invalid join type ['" . $t . "']");
        }
        return $t;
    }
    /**
     * Builds Column.
     * @param mixed $v
     * @param mixed $ad
     * @param mixed $append
     */
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
                if (empty($k = trim($k))) die("column key not allowed");
                $columns .= $k;
                if ($k != $s)
                    $columns .= " AS " . $s;
                continue;
            }
            if (is_string($s)) {
                $columns .= $ad->escape_string($s);
            } elseif (is_object($s)) {
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
            } else {
                igk_die("invalid column specification");
            }
            if ($c = getv($s, "as")) {
                $columns  .= " AS " . $c;
            }
        }
        return $columns;
    }
    /**
     * Order query extra options
     * @param IDbSQLGrammarExtraOptions $options
     * @param mixed $ad
     */
    protected static function GetExtraOptions($options, $ad)
    {
        /**
         * auto generate doc.
         * @var IDbSQLGrammarExtraOptions $options
         */
        $options = !is_object($options) ? (object)$options : $options;
        $defOrder = "ASC";
        $q = "";
        $optset = [];
        $columns = "*";
        $query = "";
        $flag = "";
        $join = "";
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
                if ($alias = igk_getv($vv, 'alias')) {
                    $join .= 'as ' . $alias . ' ';
                }
                $v_cond = igk_getv($vv, 0);
                if ($v_cond) {
                    if (is_string($v_cond)) {
                        $join .= "on (" . $v_cond . ")";
                    } else {
                        die("condition not allowed");
                    }
                }
            }
        };
        $t_data = get_robjs("Distinct|GroupBy|OrderBy|OrderByField|Columns|Limit|Joins|Skip", 0, $options);
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
                case queryConstant::OrderBy:
                    SQLQueryUtils::BuildOrderBy($v, $optset, $k, $ad, $defOrder);
                    break;
                case queryConstant::Columns:
                    $a = 0;
                    $columns = self::BuildColumn($v, $ad, $a);
                    break;
                case queryConstant::Skip:
                    $optset['Skip'] = $v;
                    break;
            }
        }
        $v_sc = igk_getv($options, 'SortColumn');
        if (!isset($optset["OrderBy"])) {
            if (isset($options->Sort) && isset($options->SortColumn)) {
                $v = strtoupper($options->Sort);
                if (strpos("ASC|DESC", $v) !== false) {
                    $q .= " ORDER BY `" . $ad->escape_string($options->SortColumn) . "` " . $v;
                    $optset["OrderBy"] = 1;
                }
            } else {
                if (isset($v_sc) && @is_array($v_sc)) {
                    foreach ($v_sc as $r => $v) {
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
        if (isset($optset['Skip'])){
            $query .= self::_BuildSkip(intval($optset['Skip']));
        }
        $query = trim($query);
        return (object)["columns" => $columns, "join" => $join, "extra" => $q . $query, "flag" => $flag];
    }
    /**
     * 
     * @param int $offset 
     * @return string 
     */
    protected static function _BuildSkip(int $offset){
        return sprintf(" OFFSET %s", $offset);
    }
    /**
     * auto generate doc.
     * @param mixed $t
     * @param mixed $adapter
     * @param mixed $separator
     * @return mixed
     */
    protected static function Key($t, $adapter, $separator = ",")
    {
        return implode($separator, array_map(
            function ($t) use ($adapter) {
                return  "`" . implode("`.`", array_map([$adapter, "escape_string"], explode(".", $t))) . "`";
            },
            array_map("trim", array_filter(explode(',', $t)))
        ));
    }
    /**
     * get group keys
     * @param mixed $columns 
     * @param string $type 
     * @param mixed $adapter 
     * @return string 
     */
    protected static function GetGroupKey($columns, string $type, $adapter): string
    {
        return SQLQueryUtils::GetGroupKey($columns, $type, $adapter);
    }
    /**
     * auto generate doc.
     * @param mixed $tbname
     * @param mixed $condition
     * @return string
     */
    public function createDeleteQuery($tbname, $condition = null)
    {
        $c = "";
        if ($condition && ($c = static::GetCondString($this->m_driver, $condition))) {
            $c = " WHERE " . $c;
        }
        return "DELETE FROM `" . $this->m_driver->escape_string($tbname) . "`" . $c . ";";
    }
    /**
     * get list of table
     * @param null|string $filter 
     * @return array 
     */
    public function listTables(?string $filter = null)
    {
        $tables = [];
        $col = null;
        $query = sprintf("SHOW TABLES%s;", $filter ? sprintf(
            ' like \'%s\'',
            $this->m_driver->escape_string($filter)
        ) : '');
        $this->m_driver->sendQuery($query, true, [
            IGKMySQLQueryResult::CALLBACK_OPTS => function ($row) use (&$tables, &$col) {
                if (is_null($col)) {
                    $col = $row->column(0);
                }
                $tables[] =  (object)["table" => $row->{$col}];
                return false;
            }
        ]);
        return $tables;
    }
    /**
     * get column string concatenation
     * @param array $s
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
    /**
     * auto generate doc.
     * @param string $table
     * @param mixed $field
     * @param string $dbname
     * @return ?object
     */
    public function get_relation(string $table, $field, string $dbname)
    {
        return [];
    }
    /**
     * get column info
     * @param string $table
     * @param string $column
     * @throws IGKException
     * @return mixed
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
    /**
     * Creates Select Expression.
     * @param mixed $table_name
     * @param mixed $column
     * @param mixed $conditions
     */
    public function createSelectExpression($table_name, $column, $conditions)
    {
        $g = $this->createSelectQuery($table_name, $conditions, [
            "Columns" => $column
        ]);
        return new DbExpression("(" . rtrim($g, ";") . ")");
    }
    /**
     * create a join operation or failed
     * @param mixed $type 
     * @param mixed $a 
     * @param mixed $b 
     * @return string 
     * @throws \IGKException 
     */
    public function createJoinOperation($type, $a, $b): string
    {
        if (preg_match("/<|>|=/", $type))
            return sprintf("%s%s%s", $a, $type, $b);
        igk_die("invalid join operator search:" . $type);
        return '';
    }

    /**
     * 
     * @param string $column 
     * @param string $column2 
     * @return string 
     */
    public function joinOnEqual(string $column, string $column2): ?string
    {
        return sprintf('%s=%s', $this->escape_string($column), $this->escape_string($column2));
    }
    /**
     * 
     * @param string $v 
     * @return string 
     */
    private function escape_string(string $v)
    {
        return $this->m_driver->escape_string($v);
    }
}
